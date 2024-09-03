<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class ResignApplicationStatusRejected extends Notification
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
        $status = $this->resignApplication->status;
        if($this->resignApplication->status == 'rejected'){
            $status = "Ditolak";
        }else if($this->resignApplication->status == 'canceled'){
            $status = "Dibatalkan";
        }
        return (new MailMessage)
            ->subject('Resign Anda ' . $status . ' ' . $position->name)
            ->markdown('mail.loan.resign-updated',['resignApplication'=> $this->resignApplication, 'status' => $status]);
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
        $status = $this->resignApplication->status;
        if($this->resignApplication->status == 'rejected'){
            $status = "ditolak";
        }else if($this->resignApplication->status == 'canceled'){
            $status = "dibatalkan";
        }
        return [
            'url'=>'resign',
            'content'=> [
                'title'=>  'Resign Anda ' . $status . ' ' . $position->name,
                'description'=> 'Perubahan status resign anda ' . $status . ' oleh ' . $position->name,
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
        $status = $this->resignApplication->status;
        if($this->resignApplication->status == 'rejected'){
            $status = "ditolak";
        }else if($this->resignApplication->status == 'canceled'){
            $status = "dibatalkan";
        }
        return OneSignalMessage::create()
            ->setSubject('Resign Anda ' . $status . ' ' . $position->name)
            ->setBody("Cek Sekarang")
            ->setData('type','loan')
            ->setData('id', $this->resignApplication->id)
            ->setData('user_id', $this->resignApplication->member->user_id);
    }
}
