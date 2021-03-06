<?php

declare(strict_types=1);

namespace NAttreid\Modal;

use NAttreid\Utils\Strings;
use Nette\Application\UI\Control;

/**
 * Dialog
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class Modal extends Control
{
	/**
	 * @param $presenter
	 * @throws \Nette\Application\AbortException
	 */
	protected function attached($presenter)
	{
		parent::attached($presenter);
		if ($this->view && !$this->presenter->isAjax()) {
			unset($this->view);
			$this->presenter->redirect('this');
		}
	}

	/**
	 * @var string
	 * @persistent
	 */
	public $view = null;

	/** @var callable[] */
	public $onClose = [];

	/** @var bool */
	private $fixed = false;

	/** @var bool */
	private $draggable = false;

	/** @var bool */
	private $redrawOnResize = true;

	public function handleOpen(): void
	{
		$this->open();
	}

	public function handleClose(): void
	{
		$this->close();
	}

	/**
	 * Nastavi posouvatelnost okna
	 * @param bool $draggeble
	 */
	public function setDraggable(bool $draggeble = true): void
	{
		$this->draggable = $draggeble;
	}

	/**
	 * Otevreni modalu
	 */
	public function open(): void
	{
		$this->view = 'true';
		$this->redrawControl('modal');
	}

	/**
	 * Znovunacteni modalu
	 */
	public function refresh(): void
	{
		$this->view = 'true';
		$this->redrawControl('modalContent');
	}

	/**
	 * Zavreni modalu
	 */
	public function close(): void
	{
		$this->view = null;
		$this->onClose();
		$this->redrawControl('modal');
	}

	/**
	 * Nastavi fixni zobrazeni
	 */
	public function fixed(): void
	{
		$this->fixed = true;
	}

	/**
	 * Nastavi prekreslovani pri zmene rozliseni
	 * @param bool $redraw
	 */
	public function redrawOnResize(bool $redraw = true): void
	{
		$this->redrawOnResize = $redraw;
	}

	public function render(): void
	{
		$this->template->layout = __DIR__ . '/modal.latte';
		$componentId = $this->getUniqueId();
		$this->template->componentId = $componentId;
		$this->template->functionName = str_replace('-', '_', $componentId);

		$this->template->view = (bool) $this->view;
		$this->template->fixed = $this->fixed;
		$this->template->draggable = $this->draggable;
		$this->template->redrawOnResize = $this->redrawOnResize;
		$this->template->handleClose = !empty($this->onClose);

		$this->template->render();
	}
}