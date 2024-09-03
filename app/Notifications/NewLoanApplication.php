<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalButton;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewLoanApplication extends Notification
{
    use Queueable;
    public $loanApplication;
    public $subject;

    /**
     * NewLoanApplication constructor.
     * @param $loanApplication
     */
    public function __construct($loanApplication)
    {
        $this->loanApplication = $loanApplication;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database', OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->loan->member->full_name .' Mengajukan Pinjaman')
            ->markdown('mail.loan.new-application',['loanApplication'=> $this->loanApplication]);
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
            'url'=>'loan',
            'content'=> [
                'title'=> $this->loanApplication->member->full_name.' mengajukan pinjaman baru',
                'description'=> $this->loan->member->full_name.' mengajukan pinjaman dengan No. ' . $this->loan->loan_number,
                'object'=> $this->loanApplication,
                'object_type'=> 'App\TsLoans'
            ],
            'icon'=> 'fa-handshake-o',
            'icon-color'=> 'red'
        ];
    }

    public function toOneSignal($notifiable)
    {
        return OneSignalMessage::create()
            ->setSubject($this->loan->member->full_name." Mengajukan Pinjaman Baru")
            ->setBody("Cek sekarang")
            ->setData('type','loan')
            ->setData('id', $this->loanApplication->id)
            ->setData('user_id', $this->loanApplication->member->user_id);
//            ->setButton(
//                OneSignalButton::create('link-1')
//                    ->text('Click here')
//                    ->icon('https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png')
//            );
    }
}
