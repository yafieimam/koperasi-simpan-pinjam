<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class WaitChangeDepositApplication extends Notification
{
	use Queueable;
	public $changeDepositApplication;
	public $subject, $via;

    /**
     * NewchangeDepositApplication constructor.
     * @param $changeDepositApplication
     * @param $via
     */
	public function __construct($changeDepositApplication)
	{
		$this->changeDepositApplication = $changeDepositApplication;
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
			->subject('Terdapat Perubahan Simpanan Menunggu Persetujuan Anda')
			->markdown('mail.loan.wait-perubahan-simpanan',['changeDepositApplication'=> $this->changeDepositApplication]);
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
			'url'=>'change-deposit-wait',
			'content'=> [
				'title'=>'Terdapat Perubahan Simpanan Menunggu Persetujuan Anda',
				'description'=> 'Perubahan simpanan ' . $this->changeDepositApplication->member->full_name . ' menunggu persetujuan Anda',
				'object'=> $this->changeDepositApplication,
				'object_type'=> 'App\ChangeDeposit'
			],
			'icon'=> 'fa-handshake-o',
			'icon-color'=> 'red'
		];
	}

	public function toOneSignal($notifiable)
	{
		return OneSignalMessage::create()
			->setSubject("Terdapat Perubahan Simpanan Menunggu Persetujuan Anda")
			->setBody("Cek sekarang")
			->setData('type','change-deposit')
			->setData('id', $this->changeDepositApplication->id)
			->setData('user_id', $this->changeDepositApplication->member['id']);
	}
}
