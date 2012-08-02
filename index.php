<?php
/**
* +--------------------------------------------------------------------------------------+
*  @Description - Fancy indexing views for apache server instead of classic views.
*  @Author      - Ye Maw.
*  @Version     - 1.0
* +--------------------------------------------------------------------------------------+ 
*/


//path to system resource url
$_configs['fl_url']       = 'http://localhost/fancylist/'; 
//home url in menu. leave blank or auto for auto detect host
$_configs['home_url']     = '';
//page title. levave blank for global setting
$_configs['title']        = '';

//show this(index.php) in listings. true for show , false for not show, leave blank for global setting.
$_configs['no_index.php'] = '';
//show about.true for show , false for not show, leave blank for global setting.
$_configs['show_about']   = '';
?>

<?php
/**
* Helper Functions.
*/


function filesize_convert($size) 
{
    if ($size <= 1024) return $size.' Bytes';
    else if ($size <= (1024*1024)) return sprintf('%d KB',(int)($size/1024));
    else if ($size <= (1024*1024*1024)) return sprintf('%.2f MB',($size/(1024*1024)));
    else return sprintf('%.2f Gb',($size/(1024*1024*1024)));
}
function get_upperone_level($current_level)
{
    $parts = explode('/',$current_level);
    $n = count($parts);
    if($n <= 4)
    {
        return null;
    }
    $url = '';
    for($i=0;$i<$n-2;$i++)
    {
        $url .= $parts[$i].'/';
    }    
    return $url;
    
}
function detect_URL()
{
    $url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
    $url .= '://'. $_SERVER['HTTP_HOST'];
    $url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    return $url;
}                                                                                                      
function detect_host()
{
    $url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
    $url .= '://'. $_SERVER['HTTP_HOST'];
    return $url;
}


/**
* Configuring
*/
$_configs['this_path'] = dirname(__FILE__);
$_configs['this_url']  = detect_URL();
$_configs['home_url']  = (empty($_configs['home_url']) or $_configs['home_url'] == 'auto') ?  detect_host() : $_configs['home_url'];
$_configs['up_url']    = get_upperone_level($_configs['this_url']);

if( empty($_configs['title']) ){
    unset($_configs['title']);
}
if( empty($_configs['no_index.php']) ){
    unset($_configs['no_index.php']);
} 
if( empty($_configs['show_about']) ){
    unset($_configs['show_about']);
}

$configs = file_get_contents($_configs['fl_url'].'configs.php');
$configs = json_decode( $configs , true );
$configs = array_merge($configs, $_configs);

$DIR = new DirectoryIterator($configs['this_path']);

?>

<!DOCTYPE html>      
<html>
    <link rel="icon" href="<?php echo $configs['fl_url'].$configs['favicon']; ?>" type="image/x-icon" />
<?php foreach ($configs['css'] as $css) : ?>
    <link href="<?php echo $configs['fl_url'].$css; ?>" type="text/css" rel="stylesheet" />
<?php endforeach; ?>
<?php foreach ($configs['js'] as $js) : ?>
    <script src="<?php echo $configs['fl_url'].$js; ?>" type="text/javascript"></script>
<?php endforeach; ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#content').css({'min-height':$('body').height()-125+'px'});
        });
    </script>
</html>
<body>
<div data-role="page" data-title="<?php echo $configs['title']; ?>">                          
    
    <div class="ui-bar ui-bar-a">
        <div data-role="controlgroup" data-type="horizontal">
            
            <a data-ajax="false" href="<?php echo $configs['this_url']; ?>" data-button="button" data-icon="refresh">Refresh</a>
            
            <?php if( $configs['up_url'] !== null ) :?>
            <a data-ajax="false" href="<?php echo $configs['up_url']; ?>" data-button="button" data-icon="arrow-u">Up One Level</a>
            <?php endif; ?>

            <a data-ajax="false" href="<?php echo $configs['home_url']; ?>" data-button="button" data-icon="home">Home</a>
            
            <a href="#" onClick="$('.directory').toggle()" data-button="button" data-icon="grid">Toggle Folders</a>
            <a href="#" onClick="$('.file').toggle()" data-button="button" data-icon="grid">Toggle Files</a>
            
        </div>
    </div>
                                                                       
	<div id="content" data-role="content">
    <?php $total_item = 0; ?>
    <ul class="sortable" data-role="listview" class="ui-listview" data-filter="true">
    <?php foreach ($DIR as $item) : ?>
        <?php if( !$item->isDot() && $item->isDir() ) : ?>            
            <li class="directory">
                <?php
                    $subDir = new RecursiveDirectoryIterator( $item );
                    $count = 0;
                    foreach( $subDir as $sub_dir )
                    {
                        $count++;;
                    }
                ?>
                <a data-ajax="false" href="<?php echo $configs['this_url'].$item->getFileName(); ?>">
                    <img class="ui-li-icon" style="width:16px;height:16px;" src="<?php echo $_configs['fl_url']; ?>icons/folder.png" />
                    <?php echo $item; ?>
                    <div class="ui-li-count"><?php echo $count; ?> items.</div>
                </a>
            </li>
            <?php $total_item ++; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php foreach ($DIR as $item) : ?>
        <?php if( $item->isFile() ) : ?>
        <?php if( ($configs['no_index.php'] !== true)  or ($item->getFilename() != 'index.php') ) : ?>
            <li class="file">
                <?php
                    $extension = explode('.',$item->getFilename());
                    $extension = end($extension);

                    if( file_exists($configs['fl_icon16_path'].$extension.".gif") )
                    {
                        $imgpath =  $configs['fl_icon16_url'].$extension.".gif";
                    }
                    else
                    {
                        $imgpath =  $configs['fl_icon16_url'].'_default.gif';
                    }
                ?>
                <a data-ajax="false" href="<?php echo $configs['this_url'].$item->getFileName(); ?>">
                    <img class="ui-li-icon" style="width:16px;height:16px;" src="<?php echo $imgpath; ?>" />
                    <?php echo $item; ?>
                    <div class="ui-li-count"><?php echo filesize_convert($item->getSize()); ?></div>
                </a>
            </li>
            <?php $total_item ++; ?>
        <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    </div>
    <div data-role="footer" data-theme="a" class="ui-bar" style="text-align:center;">
        <a href="#">Total <?php echo $total_item; ?> Items</a>
		<?php if( $configs['show_about'] ) : ?>
        <a href="#about" data-rel="dialog" data-transition="flip" data-role="button" data-icon="info">About</a>
		<?php endif; ?>
        <!--<a href="#" style="letter-spacing:1px;"><?php echo $configs['this_url']; ?></a>-->
    </div>
</div>
<?php if( $configs['show_about'] ) : ?>
<div data-role="page" data-theme="b" id="about">
    <div data-role="header">
        <h1>About Fancy List</h1>
    </div>
  
    <div data-theme="b" data-role="content">    
        <table>
            <tr>
                <td width="120px;">Description</td>
                <td width="30px;"> - </td>
                <td>Fancy listings view for Apache Web Server instead of classical views.</td>
            </tr>
            <tr>
                <td width="120px;">Used</td>
                <td width="30px;"> - </td>
                <td>JQuery Mobile and PHP Directory Iterator Class.</td>
            </tr>
			<tr>
                <td width="120px;">Version</td>
                <td width="30px;"> - </td>
                <td>1.0</td>
            </tr>
            <tr>
                <td>Date Created</td>
                <td> - </td>
                <td>8th Nov, 2011.</td>
            </tr>
            <tr>
                <td>Last Modified</td>
                <td> - </td>
                <td>9th Nov, 2011.</td>
            </tr>
			<tr>
                <td>License</td>
                <td> - </td>
                <td>GNU General Public License.</td>
            </tr>
            <tr>
                <td colspan="3">Developed by Ye Maw.</td>
            </tr>
        </table>
    </div>
    
    <div data-role="footer">
    <h3></h3>      
    </div>
</div>
<?php endif; ?>

</body>
</html>