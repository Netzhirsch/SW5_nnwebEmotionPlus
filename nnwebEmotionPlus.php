<?php

namespace nnwebEmotionPlus;

use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;

class nnwebEmotionPlus extends \Shopware\Components\Plugin {

	public static function getSubscribedEvents() {
		return [
				'Enlight_Controller_Action_PostDispatch_Widgets_Emotion' => 'registerView'
		];
	}

	public function registerView(\Enlight_Controller_ActionEventArgs $args) {
		$this->container->get('template')->addTemplateDir($this->getPath() . '/Resources/views/');
	}

	public function install(InstallContext $context) {
		$service = $this->container->get('shopware_attribute.crud_service');
		$service->update('s_emotion_attributes', 'css_id', 'string', [
				'label' => 'CSS-ID',
				'supportText' => 'Mit dieser ID kann die Einkaufswelt mit CSS angesprochen werden.',
				'displayInBackend' => true,
				'position' => 0
		]);
		$service->update('s_emotion_attributes', 'css_class', 'string', [
				'label' => 'CSS-Klasse',
				'supportText' => 'Mit dieser Klasse kann die Einkaufswelt mit CSS angesprochen werden.',
				'displayInBackend' => true,
				'position' => 1
		]);
		$service->update('s_emotion_attributes', 'auto_height', 'boolean', [
				'label' => 'Automatische Höhe',
				'supportText' => 'Die Höhe wird ignoriert und muss mit CSS gesetzt werden.',
				'displayInBackend' => true,
				'position' => 2
		]);
		
		$this->deleteCacheAndGenerateModel(['s_emotion_attributes']);
		
		$context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
	}

	public function uninstall(UninstallContext $context) {
		$service = $this->container->get('shopware_attribute.crud_service');
		$service->delete('s_emotion_attributes', 'css_id');
		$service->delete('s_emotion_attributes', 'css_class');
		$service->delete('s_emotion_attributes', 'auto_height');
		
		$this->deleteCacheAndGenerateModel(['s_emotion_attributes']);
		
		$context->scheduleClearCache(InstallContext::CACHE_LIST_DEFAULT);
	}

	private function deleteCacheAndGenerateModel($tables) {
		$metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
		$metaDataCache->deleteAll();
		Shopware()->Models()->generateAttributeModels($tables);
	}
}