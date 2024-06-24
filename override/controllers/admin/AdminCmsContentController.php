<?php

class AdminCmsContentController extends AdminCmsContentControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $this->actions_available[] = 'duplicate';
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['duplicate'] = array(
                'short' => 'Duplicate',
                'href' => self::$currentIndex.'&action=duplicate&token='.Tools::getAdminTokenLite('AdminCmsContent'),
                'desc' => $this->l('Duplicate'),
                'icon' => 'icon-copy'
            );
        }
    }

    public function processDuplicate()
    {
        $id_cms = (int)Tools::getValue('id_cms');
        $cms = new CMS($id_cms);

        if (Validate::isLoadedObject($cms)) {
            $cms->id = null;
            $cms->id_cms_category = $cms->id_cms_category;
            $cms->meta_title = array_map(function ($title) {
                return $title . ' (copy)';
            }, $cms->meta_title);
            $cms->link_rewrite = array_map(function ($link) {
                return $link . '-copy';
            }, $cms->link_rewrite);
            $cms->add();

            // Duplicate Creative Elements content if available
            if (Module::isInstalled('creativeelements')) {
                $this->duplicateCreativeElementsContent($id_cms, $cms->id);
            }

            Tools::redirectAdmin(self::$currentIndex.'&conf=19&token='.Tools::getAdminTokenLite('AdminCmsContent'));
        } else {
            $this->errors[] = $this->trans('An error occurred while attempting to duplicate the CMS page.', [], 'Admin.Notifications.Error');
        }
    }

    private function duplicateCreativeElementsContent($old_cms_id, $new_cms_id)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'creativeelements_pages WHERE page_id = '.(int)$old_cms_id;
        $result = Db::getInstance()->getRow($sql);

        if ($result) {
            $result['page_id'] = $new_cms_id;
            unset($result['id']);
            Db::getInstance()->insert('creativeelements_pages', $result);
        }
    }
}
