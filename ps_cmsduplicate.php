<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Ps_CmsDuplicate extends Module
{
    public function __construct()
    {
        $this->name = 'ps_cmsduplicate';
        $this->tab = 'administration';
        $this->version = '0.0.3';
        $this->author = 'Jaymian-Lee Reinartz';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('CMS Duplicate');
        $this->description = $this->l('Duplicate CMS pages in PrestaShop.');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionAdminControllerSetMedia') && $this->copyOverrideFiles() && $this->copyJsFiles();
    }
    
    private function copyJsFiles()
    {
        $src = __DIR__.'/views/js/';
        $dst = _PS_JS_DIR_.'admin/';
    
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
    
        return $this->recurseCopy($src, $dst);
    }
    
    public function uninstall()
    {
        return parent::uninstall() && $this->removeOverrideFiles() && $this->removeJsFiles();
    }
    
    private function removeJsFiles()
    {
        $file = _PS_JS_DIR_.'admin/ps_cmsduplicate.js';
    
        if (file_exists($file)) {
            unlink($file);
        }
    
        return true;
    }
    

    private function copyOverrideFiles()
    {
        $src = __DIR__.'/override/controllers/admin/';
        $dst = _PS_OVERRIDE_DIR_.'controllers/admin/';

        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }

        return $this->recurseCopy($src, $dst);
    }

    private function removeOverrideFiles()
    {
        $files = [
            _PS_OVERRIDE_DIR_.'controllers/admin/AdminCmsContentController.php'
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    private function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    if (!is_dir($dst . '/' . $file)) {
                        mkdir($dst . '/' . $file);
                    }
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }
}
