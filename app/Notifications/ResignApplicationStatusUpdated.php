<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class ResignApplicationStatusUpdated extends Notification
{
    use Queueable;
    public $resignApplication;
    /**
     * Create a new notification instance.
     *
     * LoanApplicationStatusUpdated constructor.
     * @param $loanApplication
     */
    public function __construct($resignApplication)
    {
        $this->resignApplication = $resignApplication;
    }

    /**
    * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database',OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $position = $position = Position::find($this->resignApplication->status_by);
        return (new MailMessage)
            ->subject('Resign Anda Disetujui ' . $position->name)
            ->markdown('mail.loan.resign-updated',['resignApplication'=> $this->resignApplication, 'status' => 'disetujui']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $position = $position = Position::find($this->resignApplication->status_by);
        return [
            'url'=>'resign-member',
            'content'=> [
                'title'=> 'Resign Anda Disetujui ' . $position->name,
                'description'=> 'Perubahan status resign anda disetujui oleh ' . $position->name,
                'object'=> $this->resignApplication,
                'object_type'=> 'App\Resign'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $position = $position = Position::find($this->resignApplication->status_by);
        return OneSignalMessage::create()
            ->setSubject('Resign Anda Disetujui ' . $position->name)
            ->setBody("Cek Sekarang")
            ->setData('type','resign')
            ->setData('id', $this->resignApplication->id)
            ->setData('user_id', $this->resignApplication->member->user_id);
    }
}
