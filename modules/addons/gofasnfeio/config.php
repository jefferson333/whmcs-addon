<?php
if (!defined("WHMCS")){die();}
use WHMCS\Database\Capsule;
if( !function_exists('gofasnfeio_config') ) {
    if( !function_exists('gnfe_customfields_dropdow') ) {
        function gnfe_customfields_dropdow()
        {
            $customfields_array = array();
            foreach (Capsule::table('tblcustomfields')->where('type', '=', 'client')->get(array('fieldname', 'id')) as $customfield) {
                $customfields_array[] = $customfield;
            }
            $customfields = json_decode(json_encode($customfields_array), true);
            if (!$customfields) {
                $dropFieldArray = array('0' => 'database error');
            }elseif (count($customfields) >= 1) {
                $dropFieldArray = array('0' => 'selecione um campo');
                foreach ($customfields as $key => $value){
    
                    $dropFieldArray[$value['id']] = $value['fieldname'];
                }
            } else {
                $dropFieldArray = array('0' => 'nothing to show');
            }
            return $dropFieldArray;
        }
    }
    function gofasnfeio_config() {
        $module_version = '1.2.4';
        $module_version_int = (int)preg_replace('/[^0-9]/', '', $module_version);

       /// REMOVER VERIFICAÇÃO APÓS VERSÃO 2.0
       $verificarEmail = Capsule::table('tbladdonmodules')->where( 'module', '=', 'gofasnfeio' )->where( 'setting', '=', 'gnfe_email_nfe_config' )->count();
       if(empty($verificarEmail)){
           // echo "vazio";
           try {
                Capsule::table('tbladdonmodules')->insert(array('module' => 'gofasnfeio', 'setting' => 'gnfe_email_nfe_config', 'value' => 'on'));
            }catch (\Exception $e) {
                $e->getMessage();
            }
       }////// FIM VERIFICAÇÃO

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if ( stripos( $actual_link, '/configaddonmods.php') ) {
            $whmcs_url__ = str_replace("\\",'/',(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'].substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT'])));
            $admin_url = $whmcs_url__.'/';
            $vtokens = explode('/', $actual_link);
            $whmcs_admin_path = '/'.$vtokens[sizeof($vtokens)-2].'/';
            $whmcs_url = str_replace( $whmcs_admin_path, '', $admin_url).'/';
            foreach( Capsule::table('tblconfiguration') -> where('setting', '=', 'gnfewhmcsurl') -> get( array( 'value','created_at' ) ) as $gnfewhmcsurl_ ) {
                $gnfewhmcsurl					= $gnfewhmcsurl_->value;
                $gnfewhmcsurl_created_at		= $gnfewhmcsurl_->created_at;
            }
            foreach( Capsule::table('tblconfiguration') -> where('setting', '=', 'gnfe_email_nfe') -> get( array( 'value' ) ) as $gnfe_email_nfe_ ) {
                $gnfe_email_nfe					= $gnfewhmcsurl_->value;
            }

            foreach( Capsule::table('tblconfiguration') -> where('setting', '=', 'gnfewhmcsadminurl') -> get( array( 'value','created_at' ) ) as $gnfewhmcsadminurl_ ) {
                $gnfewhmcsadminurl				= $gnfewhmcsadminurl_->value;
                $gnfewhmcsadminurl_created_at	= $gnfewhmcsurl_->created_at;
            }
            foreach( Capsule::table('tblconfiguration') -> where('setting', '=', 'gnfewhmcsadminpath') -> get( array( 'value','created_at' ) ) as $gnfewhmcsadminpath_ ) {
                $gnfewhmcsadminpath				= $gnfewhmcsadminpath_->value;
                $gnfewhmcsadminpath_created_at	= $gnfewhmcsurl_->created_at;
            }
            if ( !$gnfe_email_nfe ) {
                try {
                    Capsule::table('tblconfiguration')->insert(array('setting' => 'gnfe_email_nfe', 'value' => 'Active', 'created_at' => date("Y-m-d H:i:s") , 'updated_at' => date("Y-m-d H:i:s")));
                }
                catch (\Exception $e) {
                    $e->getMessage();
                }
            }
            if ( !$gnfewhmcsurl ) {
                // Set config
                try { Capsule::table('tblconfiguration')->insert(array('setting' => 'gnfewhmcsurl', 'value' => $whmcs_url, 'created_at' => date("Y-m-d H:i:s") , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) { $e->getMessage(); }
                try { Capsule::table('tblconfiguration')->insert(array('setting' => 'gnfewhmcsadminurl', 'value' => $admin_url, 'created_at' => date("Y-m-d H:i:s") , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) { $e->getMessage(); }

                try { Capsule::table('tblconfiguration')->insert(array('setting' => 'gnfewhmcsadminpath', 'value' => $whmcs_admin_path, 'created_at' => date("Y-m-d H:i:s") , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) { $e->getMessage(); }
            }
            // Update Settings
            if ( $gnfewhmcsurl and ($whmcs_url !== $gnfewhmcsurl) ) {
                try { Capsule::table('tblconfiguration')->where( 'setting', 'gnfewhmcsurl')->update(array('value' => $whmcs_url, 'created_at' =>  $gnfewhmcsurl_created_at , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) {$e->getMessage();}
            }
            if ( $gnfewhmcsadminurl and ($admin_url !== $gnfewhmcsadminurl) ) {
                try { Capsule::table('tblconfiguration')->where( 'setting', 'gnfewhmcsadminurl')->update(array('value' => $admin_url, 'created_at' =>  $gnfewhmcsadminurl_created_at , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) {$e->getMessage();}
            }
            if ( $gnfewhmcsadminpath and ($whmcs_admin_path !== $gnfewhmcsadminpath) ) {
                try { Capsule::table('tblconfiguration')->where( 'setting', 'gnfewhmcsadminpath')->update(array('value' => $whmcs_admin_path, 'created_at' =>  $gnfewhmcsadminpath_created_at , 'updated_at' => date("Y-m-d H:i:s")));}
                catch (\Exception $e) {$e->getMessage();}
            }
        }
        // Verify available updates
        $available_update_message = '<p style="font-size: 14px;color:red;"><i class="fas fa-exclamation-triangle"></i> Nova versão disponível no <a style="color:#CC0000;text-decoration:underline;" href="https://github.com/nfe/whmcs-addon/releases" target="_blank">Github</a></p>';

        if( !function_exists('gnfe_verifyInstall') ) {
            function gnfe_verifyInstall() {
                if ( !Capsule::schema()->hasTable('gofasnfeio') ) {
                    try {
                        Capsule::schema()->create('gofasnfeio', function($table) {
                            // incremented id
                            $table->increments('id');
                            // whmcs info
                            $table->string('invoice_id');
                            $table->string('user_id');
                            $table->string('nfe_id');
                            $table->string('status');
                            $table->string('services_amount');
                            $table->string('environment');
                            $table->string('flow_status');
                            $table->string('pdf');
                            $table->string('rpsSerialNumber');
                            $table->string('rpsNumber');
                            $table->string('created_at');
                            $table->string('updated_at');
                        });
                    }
                    catch (\Exception $e) {
                        $error .= "Não foi possível criar a tabela do módulo no banco de dados: {$e->getMessage()}";
                    }
                }
                // Added in v 1 dot 1 dot 3
                if(!Capsule::schema()->hasColumn('gofasnfeio', 'rpsNumber')){
                    try {
                        Capsule::schema()->table('gofasnfeio', function($table){$table->string('rpsNumber');});
                    }
                    catch (\Exception $e) {
                        $error .= "Não foi possível atualizar a tabela do módulo no banco de dados: {$e->getMessage()}";
                    }
                }

                if(!$error) {
                    return array('sucess'=>1);
                }
                elseif($error) {
                    return array('error'=>$error);
                }
            }
        }
        gnfe_verifyInstall();

        $intro = array('intro' => array(
            'FriendlyName' => '',
            'Description' => '<h4 style="padding-top: 5px;">Módulo Nota Fiscal NFE.io para WHMCS v'.$module_version.'</h4>
					'.$available_update_message.'',
        ));
        $api_key = array('api_key' => array(
            'FriendlyName' => 'API Key',
            'Type' => 'text',
            'Description' => '<a href="https://app.nfe.io/account/apikeys" style="text-decoration:underline;" target="_blank">Obter chave de acesso</a>',
        ));
        $company_id = array('company_id' => array(
            'FriendlyName' => 'ID da Empresa',
            'Type' => 'text',
            'Description' => '<a href="https://app.nfe.io/companies/" style="text-decoration:underline;" target="_blank">Obter ID da empresa</a>',
        ));
        $service_code = array('service_code' => array(
            'FriendlyName' => 'Código de Serviço',
            'Type' => 'text',
            'Description' => '<a style="text-decoration:underline;" href="https://nfe.io/docs/nota-fiscal-servico/conceitos-nfs-e/#o-que-e-codigo-de-servico" target="_blank">O que é Código de Serviço?</a>',
        ));
        $rps_serial_number = array('rps_serial_number' => array(
            'FriendlyName' => 'Série do RPS',
            'Type' => 'text',
            'Default' => 'IO',
            'Description' => '<a style="text-decoration:underline;" href="https://nfe.io/docs/nota-fiscal-servico/conceitos-nfs-e/" target="_blank">Saiba mais</a>',
        ));
        $rps_number = array('rps_number' => array(
            'FriendlyName' => 'Número do RPS',
            'Type' => 'text',
            'Default' => 'zero',
            'Description' => 'O número RPS da NFE mais recente gerada.<br>Deixe em branco e o módulo irá preencher esse campo após a primeira emissão. Não altere o valor a menos que tenha certeza de como funciona essa opção. <a style="text-decoration:underline;" href="https://nfe.io/docs/nota-fiscal-servico/conceitos-nfs-e/" target="_blank">Saiba mais.</a>',
        ));
        $issue_note = array('issue_note' => array(
            'FriendlyName' => 'Quando emitir NFE',
            'Type' => 'radio',
            'Options' => 'Manualmente, Quando a Fatura é Gerada,Quando a Fatura é Paga',
            'Default' => 'Quando a Fatura é Paga',
        ));
        $issue_note_after = array('issue_note_after' => array(
            'FriendlyName' => 'Agendar Emissão',
            'Type' => 'text',
            'Default' => '',
            'Description' => '<br>Número de dias após o pagamento da fatura que as notas devem ser emitidas. <span style="color:#c00">Preencher essa opção desativa a opção anterior.</span>',
        ));
        $gnfe_email_nfe_config = array('gnfe_email_nfe_config' => array(
            'FriendlyName' => 'Disparar e-mail com a nota',
            'Type' => 'yesno',
            'Default' => 'yes',
            'Description' => 'Permitir o disparo da nota fiscal via NFE.io para o e-mail do usuário.',
        ));
        $cancel_invoice_cancel_nfe = array('cancel_invoice_cancel_nfe' => array(
            'FriendlyName' => 'Cancelar NFE',
            'Type' => 'yesno',
            'Default' => 'yes',
            'Description' => 'Cancela a nota fiscal quando a fatura cancelada',
        ));
        $debug = array('debug' => array(
            'FriendlyName' => 'Debug',
            'Type' => 'yesno',
            'Default' => 'yes',
            'Description' => 'Marque essa opção para salvar informações de diagnóstico no <a target="_blank" style="text-decoration:underline;" href="'.$admin_url.'systemmodulelog.php">Log de Módulo</a>',
        ));
        $insc_municipal = array('insc_municipal' => array(
            'FriendlyName' => 'Inscrição Municipal',
            'Type' => 'dropdown',
            'Options' => gnfe_customfields_dropdow(),
            'Description' => 'Escolha o campo personalizado de Inscrição Municipal'));
        
        $footer = array('footer' => array(
                'FriendlyName' => '',
                'Description' => '&copy; '.date('Y').' <a target="_blank" title="Para suporte utilize o github" href="https://github.com/nfe/whmcs-addon/issues">Suporte módulo</a>',
        ));
        $fields = array_merge($intro,$api_key,$company_id,$service_code,$rps_serial_number,$rps_number,$issue_note,$issue_note_after,$gnfe_email_nfe_config,$cancel_invoice_cancel_nfe,$debug,$insc_municipal,$footer);
        $configarray = array(
            "name" => "NFE.io",
            "description" => "Módulo Nota Fiscal NFE.io para WHMCS",
            "version" => $module_version,
            "author" => '<a title="NFE.io Nota Fiscal WHMCS" href="https://github.com/nfe/whmcs-addon/" target="_blank" ><img src="'.$whmcs_url.'modules/addons/gofasnfeio/lib/logo.png"></a>',
            "fields" => $fields,
        );
        return $configarray;
    }
}