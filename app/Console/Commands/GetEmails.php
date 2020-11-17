<?php

namespace App\Console\Commands;

use App\Contact;
use App\Customer;
use App\Event;
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
        $settings = json_decode(Storage::disk('local')->get('settings.json'), true);
         dd(Storage::disk('local')->get('settings.json'))   ;
        $mailMail = $settings['MAIN_MAIL_ADDRESS'];

        $cm = new Clientmanager();

        // $settings['IMAP_HOST'] = 'imap.googlemail.com'
        // $settings['MAIL_ENCRYPTION'] = ssl
        // $settings['MAIL_PORT'] = 993

        /** @var Client $message */
        $client = $cm->make([
            'host'           => $settings['IMAP_HOST'],
            'port'           => $settings['IMAP_PORT'],
            'encryption'     => $settings['IMAP_ENCRYPTION'],
            'validate_cert'  => false,
            'protocol'       => 'imap',
            'username'       => $settings['IMAP_USERNAME'],
            'password'       => $settings['IMAP_PASSWORD'],

        ]);
        $client->connect();

        /** @var FolderCollection $folders */
        $folders = $client->getFolders();
        /** @var Folder $folder */
        foreach($folders as $folder){

            /** @var MessageCollection $aMessage */

            if ( $folder->path == 'INBOX') {
                /** @var  Message $message */
                $messagesToMailEmail = $folder->messages()->unseen()->to($mailMail)->get();

                $this->incomingEmail($messagesToMailEmail);
                $messagesFromMainEmail = $folder->messages()->unseen()->from($mailMail)->get();
                $this->outGoingEmail($messagesFromMainEmail);

            }
        }
     }

    private function outGoingEmail($messages)
    {
        foreach($messages as $message){
            $address = $message->to[0]->mail;
            $customer = $this->searchCustomersEmails($message, $address);
            if ($customer)
            {
                $this->createEmailEvent($customer, $message);
            }
        }
    }

    private function incomingEmail($messages)
    {
        foreach($messages as $message){

            if($message->subject == 'Fwd: TMI Productions "פניה דרך האתר"') {

                $this->createLead($message);
            }else{

                $address = $message->from[0]->mail;
                $customer =   $this->searchCustomersEmails($message, $address);
                if ($customer)
                {
                    $this->createEmailEvent($customer, $message);
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

    private function searchCustomersEmails($message, $address) {
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
            ->get();
        if (count($customer) > 0)
        {
            $contacts = $customer->getIterator();

            foreach ($contacts as $contact) {

                    if ($contact->contact_email === $message->from[0]->mail) {
                        $this->createEmailEvent($contact, $message);
                        return $contact;
                    }
            }
            return $contacts[0];
        }

        return false;
    }

    private function createEmailEvent($customer, $message) {


        /** @var  Message $message */
        $emailData = [
            'title' => strip_tags($message->getSubject()),
            'details' => strip_tags($message->getTextBody()),
            'start_date' => Carbon::parse($message->date->toArray()['formatted']),
            'type_id' => $this::MAIL_TYPE,
            'contact_id' => $customer->contact_id,
            'user_id' => 1];
        Event::create($emailData);


    }
}
