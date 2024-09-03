<?php

namespace App\Notifications;

use App\Position;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class PenambahanSimpananApplicationStatusRejected extends Notification
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
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == 'rejected'){
            $status = "Ditolak";
        }
        return (new MailMessage)
            ->subject('Penambahan Simpanan Anda ' . $status)
            ->markdown('mail.deposit.add-status-updated',['depositApplication'=> $this->depositApplication, 'status' => $status]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == 'rejected'){
            $status = "Ditolak";
        }
        return [
            'url'=>'add-deposit',
            'content'=> [
                'title'=> 'Penambahan simpanan Anda ' . $status . ' Supervisor Koperasi',
                'description'=> 'Perubahan status penambahan simpanan anda ' . $status . ' oleh Supervisor Koperasi',
                'object'=> $this->depositApplication,
                'object_type'=> 'App\TsDeposits'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        $status = $this->depositApplication->status;
        if($this->depositApplication->status == 'rejected'){
            $status = "Ditolak";
        }
        return OneSignalMessage::create()
            ->setSubject('Penambahan Simpanan Anda ' . $status . ' Supervisor Koperasi')
            ->setBody("Cek Sekarang")
            ->setData('type','add-deposit')
            ->setData('id', $this->depositApplication->id)
            ->setData('user_id', $this->depositApplication->member->user_id);
    }
}
