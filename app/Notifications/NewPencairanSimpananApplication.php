<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class NewPencairanSimpananApplication extends Notification
{
	use Queueable;
	public $depositApplication;
	public $subject;
    public $via;

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
			->subject('Terdapat Pengajuan Pencairan Simpanan Baru')
			->markdown('mail.loan.pencairan-simpanan',['depositApplication'=> $this->depositApplication]);
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
			'url'=>'pencairan-deposit',
			'content'=> [
				'title' => 'Terdapat Pencairan Simpanan Baru',
				'description'=> $this->depositApplication->member->full_name.' mengajukan pencairan simpanan',
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
			->setSubject("Terdapat Pencairan Simpanan Baru")
			->setBody("Cek sekarang")
			->setData('type','pencairan-simpanan')
			->setData('id', $this->depositApplication->id)
			->setData('user_id', $this->depositApplication->member['id']);
	}
}
