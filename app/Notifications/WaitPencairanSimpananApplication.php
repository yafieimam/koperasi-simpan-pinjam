<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class WaitPencairanSimpananApplication extends Notification
{
	use Queueable;
	public $depositApplication;
	public $subject;

    /**
     * NewresignApplication constructor.
     * @param $resignApplication
     * @param array $via
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
			->subject('Terdapat Pencairan Simpanan Menunggu Persetujuan Anda')
			->markdown('mail.loan.wait-pencairan-simpanan',['depositApplication'=> $this->depositApplication]);
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
			'url'=>'pencairan-deposit-wait',
			'content'=> [
				'title' => 'Terdapat Pencairan Simpanan Menunggu Persetujuan Anda',
				'description'=> 'Pencairan simpanan ' . $this->depositApplication->member->full_name . ' menunggu persetujuan Anda',
				'object'=> $this->depositApplication,
				'object_type'=> 'App\PencairanSimpanan'
			],
			'icon'=> 'fa-handshake-o',
			'icon-color'=> 'red'
		];
	}

	public function toOneSignal($notifiable)
	{
		return OneSignalMessage::create()
			->setSubject("Terdapat Pencairan Simpanan Menunggu Persetujuan Anda")
			->setBody("Cek sekarang")
			->setData('type','pencairan-simpanan')
			->setData('id', $this->depositApplication->id)
			->setData('user_id', $this->depositApplication->member['id']);
	}
}
