<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class ChangeDepositApplicationStatusRejected extends Notification
{
    use Queueable;
    public $depositApplication;
    /**
     * Create a new notification instance.
     *
     * LoanApplicationStatusUpdated constructor.
     * @param $loanApplication
     */
    public function __construct($depositApplication)
    {
        $this->depositApplication = $depositApplication;
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
        $position = $position = Position::find($this->depositApplication->status_by);
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == 2){
            $status = "Ditolak";
        }
        return (new MailMessage)
            ->subject('Perubahan Simpanan Anda ' . $status . ' ' . $position->name)
            ->markdown('mail.deposit.change-status-updated',['depositApplication'=> $this->depositApplication, 'status' => $status]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == false){
            $status = "Ditolak";
        }
        return [
            'url'=>'change-deposit',
            'content'=> [
                'title'=> 'Perubahan simpanan Anda ' . $status . ' ' . $position->name,
                'description'=> 'Perubahan status perubahan simpanan anda ' . $status . ' oleh ' . $position->name,
                'object'=> $this->depositApplication,
                'object_type'=> 'App\ChangeDeposit'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $position = $position = Position::find($this->depositApplication->status_by);
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == false){
            $status = "Ditolak";
        }
        return OneSignalMessage::create()
            ->setSubject('Perubahan Simpanan Anda ' . $status . ' ' . $position->name)
            ->setBody("Cek Sekarang")
            ->setData('type','change-deposit')
            ->setData('id', $this->depositApplication->id)
            ->setData('user_id', $this->depositApplication->member->user_id);
    }
}
