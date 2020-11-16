<?php

namespace App\Console\Commands;

use App\Event;
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

        $cm = new Clientmanager();
        // $settings['MAIL_HOST'] = 'imap.googlemail.com'
        //$settings['MAIL_ENCRYPTION'] = ssl
       // $settings['MAIL_PORT'] = 993
        /** @var Client $message */
        $client = $cm->make([
            'host'           => $settings['MAIL_HOST'],
            'port'           => $settings['MAIL_PORT'],
            'encryption'     => $settings['MAIL_ENCRYPTION'],
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

            //Get all Messages of the current Mailbox $oFolder
            /** @var MessageCollection $aMessage */

            if ( $folder->path == 'INBOX') {
                /** @var  Message $message */
                $messages = $folder->messages()->unseen()->get();

                foreach($messages as $message){
                      $customer = $this->searchCustomersEmails($message->from[0]->mail);
                      if (count($customer) > 0)
                      {
                          $this->createEmailEvent($customer, $message);
                      }
                    dd($message->from[0]->mail);
//                    dump($oMessage->getSubject().'<br />');
//                    dump('Attachments: '.$oMessage->getAttachments()->count().'<br />');
//                    dump($oMessage->getHTMLBody(true));
                }
            }
        }
        die;
    }

    private function searchCustomersEmails($mail) {

        return DB::table('customers', 'cu')
            ->leftJoin('contacts', 'cu.id', '=', 'contacts.customer_id')
            ->select(
                'cu.id',
                'cu.name',
                'cu.email',
                'contacts.email as contact_email',
                'contacts.id as contact_id',
                'contacts.first_name as contact_first_name',
                'contacts.last_name as contact_last_name')
            ->where('cu.email', $mail)
            ->get()->toArray();
    }

    private function createEmailEvent($customer, $message) {
        /** @var  Message $message */

        $start_date = new \DateTime();
        $emailData = [
            'title' => strip_tags($message->getSubject()),
            'details' => strip_tags($message->getTextBody()),
            'start_date' => $start_date,
            'type_id' => $this::MAIL_TYPE,
            'contact_id' => $customer[0]->contact_id,
            'user_id' => 1];
        Event::create($emailData);

        dd($customer);
    }
}
