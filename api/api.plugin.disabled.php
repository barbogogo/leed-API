<?php
/*
@name api
@author Barbogogo <barbogogo@barbogogo.fr>
@link http://www.barbogogo.fr/projets/applications-leed/leed-api/
@licence CC by nc sa <a href="http://creativecommons.org/licenses/by-nc-sa/2.0/fr/">http://creativecommons.org/licenses/by-nc-sa/2.0/fr/</a>
// @version 0.11
@description Le plugin API permet de gérer ses flux Leed via une application externe
*/

require_once('./plugins/api/constantAPI.php');

function api_plugin_setting_link(&$myUser){
echo '<li><a class="toggle" href="#leedAPIBloc">'._t('P_LEEDAPI_TITLE').'</a></li>';
}

function api_plugin_setting_bloc(&$myUser)
{
    echo '
        <section id="leedAPIBloc" class="leedAPIBloc" style="display:none;">
        <h2>'._t('P_LEEDAPI_TITLE').'</h2>

        <section class="preferenceBloc">
        <h3>'._t('P_LEEDAPI_SUBTITLE').'</h3>';

    // La clé n'est affichée que si le plugin est activé
    if(PLUGIN_ENABLED == 1)
    {
        echo '<p>'._t('P_LEEDAPI_DESCRIPTION_KEY').'</p>';
        echo '<p>'._t('P_LEEDAPI_TEXT_KEY').' <b>'.KEY.'</b>.</p>';
    }
        
    echo '
        </section>
        </section>
        ';
}

$myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
if($myUser!=false) 
{
    Plugin::addHook('setting_post_link', 'api_plugin_setting_link');
    Plugin::addHook('setting_post_section', 'api_plugin_setting_bloc');
}
?>
