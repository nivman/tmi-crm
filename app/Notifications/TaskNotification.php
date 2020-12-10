<?php

namespace App\Notifications;

use App\Events\EmailEvent;
use App\Events\TaskEvent;
use App\Helpers\Date;
use App\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $task;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task)
    {

        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $serverIp = env('SERVER_IP');

        $format_date = Date::convertToViewFormat($this->task->start_date);
        $task = ['id'=>$this->task->id, 'name' => $this->task->name, 'start_date' => $format_date];

        event( new TaskEvent($task));

        return (new MailMessage)
                    ->theme('default')
                    ->greeting('יש משימה לעשות')
                    ->subject($this->task->name)
                    ->line($this->task->name)
                    ->line($format_date.' : זמן התחלה ')
                    ->action('למשימה', url("http://{$serverIp}/tasks/edit/email/{$this->task->id}"))
                    ->line($this->task->details);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
