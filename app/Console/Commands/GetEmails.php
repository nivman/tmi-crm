<?php

namespace App\Console\Commands;

use App\Contact;
use App\Customer;
use App\Event;
use App\File;
use Carbon\Carbon;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\FolderCollection;
use Webklex\PHPIMAP\Support\MessageCollection;

class GetEmails extends Command
{
    const MAIL_TYPE = 3;
    const MAIN_SYSTEM_CUSTOMER = 36;
    private $settings = null;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get emails form chosen mailbox';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->settings = json_decode(Storage::disk('local')->get('settings.json'), true);

        $mailMail = $this->settings['MAIN_MAIL_ADDRESS'];

        $cm = new Clientmanager();

        // $settings['IMAP_HOST'] = 'imap.googlemail.com'
        // $settings['MAIL_ENCRYPTION'] = ssl
        // $settings['IMAP_PORT'] = 993

        /** @var Client $message */
        $client = $cm->make([
            'host'           => $this->settings['IMAP_HOST'],
            'port'           => $this->settings['IMAP_PORT'],
            'encryption'     => $this->settings['IMAP_ENCRYPTION'],
            'validate_cert'  => false,
            'protocol'       => 'imap',
            'decoder' => false,
            'username'       => $this->settings['IMAP_USERNAME'],
            'password'       => $this->settings['IMAP_PASSWORD'],

        ]);
        $client->connect();
        /** @var Folder $folder */
        $folder = $client->getFolder('INBOX');

        $messagesToMailEmail = $folder->messages()->unseen()->to($mailMail)->get();
        $this->incomingEmail($messagesToMailEmail);

        $messagesFromMainEmail = $folder->messages()->unseen()->from($mailMail)->get();
        $this->outGoingEmail($messagesFromMainEmail);

        $mailsToRemove = $folder->messages()->unseen()->get();
        foreach($mailsToRemove as $message){
            $message->delete();
        }
    }

    private function outGoingEmail($messages)
    {
        foreach($messages as $message){

            if($message->to[0]->mail === $this->settings['IMAP_USERNAME'] && $message->subject === 'הקלטה') {
                $this->createRecordEmailEvent($message);

            }else{
                $addresses = $message->to;
                foreach ($addresses as $key => $address) {

                    if($address->mail) {
                        $customer = $this->searchCustomersEmails($message, $address->mail);

                        if ($customer)
                        {
                            $this->createEmailEvent($customer, $message);
                        }
                    }
                }
//                $customer = $this->searchCustomersEmails($message, $address);
//
//                if ($customer)
//                {
//                    $this->createEmailEvent($customer, $message);
//                }
            }

        }
    }

    private function incomingEmail($messages)
    {
        foreach($messages as $message){

            if($message->subject == 'TMI Productions "פניה דרך האתר"') {

                $this->createLead($message);
            }else{

                $addresses = $message->from;
                foreach ($addresses as $key => $address) {

                    if($address->mail) {
                        $customer = $this->searchCustomersEmails($message, $address->mail);

                        if ($customer)
                        {
                            $this->createEmailEvent($customer, $message);
                        }
                    }
                }
            }
        }
    }
    private function createLead($message) {
        /** @var  Message $message */
         $email = [];
         $phone = [];
         $myStr = str_replace(array("\r","\n"), "", $message->getTextBody());
         preg_match('/From:.+ <(.+)>/', $myStr, $email);
         preg_match('/טלפון(\d+)/', $myStr, $phone);

        $v =['name' => 'ליד חדש מהאתר', 'email' => $email[1], 'phone' => $phone[1], 'user_id' => 1, 'is_lead' =>1];
        $customer = Customer::create($v);
        $contact = new Contact();

        $contact = $contact->createNewContact($v, $customer->id);

        $customer->contact_id = $contact->id;
        $this->createEmailEvent($customer, $message);
    }

    private function searchCustomersEmails($message, $address, $createFile = false)
    {

        $customer = DB::table('customers', 'cu')
            ->leftJoin('contacts', 'cu.id', '=', 'contacts.customer_id')
            ->select(
                'cu.id',
                'cu.name',
                'cu.email',
                'contacts.email as contact_email',
                'contacts.id as contact_id',
                'contacts.first_name as contact_first_name',
                'contacts.last_name as contact_last_name')
            ->where('cu.email', $address)
            ->orWhere('contacts.email', $address)
            ->get();
        if (count($customer) > 0)
        {
            $contacts = $customer->getIterator();

            foreach ($contacts as $contact) {

                    if ($contact->contact_email === $message->from[0]->mail) {
                        return $contact;
                    }
            }
            return $contacts[0];
        }

        return false;
    }

    private function createEmailEvent($customer, $message, $createFile = false)
    {
        /** @var  Message $message */
        $emailData = [
            'title' => strip_tags($message->getSubject()),
            'details' => strip_tags($message->getTextBody()),
            'start_date' => Carbon::parse($message->date->toArray()['formatted']),
            'end_date' => Carbon::parse($message->date->toArray()['formatted'])->addMinutes(30),
            'type_id' => $this::MAIL_TYPE,
            'contact_id' => $customer->contact_id,
            'user_id' => 1];
        $event = Event::create($emailData);

        if ($createFile) {

            $this->saveFileInDb($message, $event, $createFile);

        }
    }


    /** @var  Message $message */
    private function createRecordEmailEvent($message)
    {
        $path = storage_path("app/public/upload/");

        if(!is_dir($path)){
            Storage::makeDirectory("public/upload/", 0775, true);
        }
        $attachments = $message->getAttachments();
        $path = storage_path("app/public/upload/".$this::MAIN_SYSTEM_CUSTOMER);

        if(!is_dir($path)){
            Storage::makeDirectory("public/upload/".$this::MAIN_SYSTEM_CUSTOMER, 0775, true);
        }
        $attachments->each(function ($attachment) use($message){

            /** @var \Webklex\PHPIMAP\Attachment $attachment */
            $attachment->save(storage_path("app/public/upload/".$this::MAIN_SYSTEM_CUSTOMER.'/'));
            $customer = $this->searchCustomersEmails($message, $this->settings['MAIN_MAIL_ADDRESS'], $attachment->getName());
            $this->createEmailEvent($customer, $message, $attachment->getName());
        },);
    }

    private function saveFileInDb($message, $event, $createFile)
    {
        $fileData = ['name'=> $createFile, 'customer_id' => $this::MAIN_SYSTEM_CUSTOMER, 'event_id' => $event->id];
        File::create($fileData);
    }
}
