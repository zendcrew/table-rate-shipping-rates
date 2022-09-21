<?php
$import_export = $page['import_export'];
$sections = array();
$last = -1;
foreach ($page['sections'] as $section) {

    if ($section['subsection'] != true) {
        $last++;
        $sections[$last] = $section;
    } else {
        $sections[$last]['subsections'][] = $section;
    }
}
if ($import_export['enable'] == true) {
    $last++;
    $sections[$last] = array(
        'id' => 'rn_import_export',
        'title' => $import_export['title'],
        'subsection' => false,
        'group' => 0,
        'fields' => array('')
    );
}
?>
<div data-active="<?php echo esc_attr($page['tab']); ?>" class="rn-option-page-tabs">
    <ul class="rn-option-page-tabs-list">
        <?php
        $data_key = 0;    
        foreach ($sections as $section) {
            $data_key++;

            if(!isset($page['group'])){
                $page['group'] = 0;
            }
            
            $is_not_active_group = !($page['group'] == $section['group']);
            
            $menu_url = '#' . esc_attr($section['id']);
            
            if ($is_not_active_group == true) {
               
                $url_base = $page['url_base'];
                
                if(isset($page['instance_id'])){
                    $url_base = $page['url_base']. '&' . $page['instance_key'] . '=' . $page['instance_id'];
                }
                if(!$page['use_slug_url']){
                    $menu_url = admin_url() . $url_base . '&'. $page['tabs_key'].'=' . $data_key;
                }else{
                    $menu_url = admin_url() . $url_base . '?page=' . $page['slug'] . '&'. $page['tabs_key'].'=' . $data_key;
                }

            }
            ?>
            <li id="rn_list_<?php echo esc_attr( $section['id']); ?>" data-key="<?php echo esc_attr($data_key); ?>" class="rn-option-li<?php echo (isset($section['subsections']) && count($section['subsections']) > 0) ? ' haschild' : ''; ?><?php echo (count($section['fields']) == 0) ? ' empty-group' : ''; ?>">
                <a<?php echo (!$is_not_active_group) ? ' data-active-group="yes"' : ''; ?> id="rn_link_<?php echo esc_attr( $section['id']); ?>" href="<?php echo esc_url($menu_url); ?>"><?php echo esc_html($section['title']); ?></a>
                <?php
                if ((isset($section['subsections']) && count($section['subsections']) > 0) || $section['id'] == 'rn_import_export') {
                    ?>
                    <ul id="rn_list_con_<?php echo esc_attr( $section['id']); ?>" class="rn-option-page-tabs-sublist">
                        <?php
                        if (isset($section['subsections'])) {
                            foreach ($section['subsections'] as $subsection) {
                                $data_key++;
                                $menu_url = '#' . esc_attr($subsection['id']);
                                $is_not_active_group = !($page['group'] == $subsection['group']);
                                if ($is_not_active_group == true) {
                                    
                                    $url_base = $page['url_base'];
                
                                    if(isset($page['instance_id'])){
                                        $url_base = $page['url_base']. '&' . $page['instance_key'] . '=' . $page['instance_id'];
                                    }
                                    if(!$page['use_slug_url']){
                                        $menu_url = admin_url() . $url_base . '&'. $page['tabs_key'].'=' . $data_key;
                                    }else{
                                        $menu_url = admin_url() . $url_base . '?page=' . $page['slug'] . '&'. $page['tabs_key'].'=' . $data_key;
                                    }
                                }
                                ?>  
                        <li id="rn_list_<?php echo esc_attr( $subsection['id']); ?>" data-key="<?php echo esc_attr($data_key); ?>" class="rn-option-subli"><a<?php echo (!$is_not_active_group) ? ' data-active-group="yes"' : ''; ?> id="rn_link_<?php echo esc_attr( $subsection['id']); ?>" href="<?php echo esc_url($menu_url);
                 ?>"><?php echo esc_html($subsection['title']); ?></a></li>
                                    <?php
                                }
                            }
                            ?>
                    </ul><!-- .rn-option-page-tabs-sublist -->
                    <?php
                }
                ?>
            </li>
            <?php
        }
        ?>   
    </ul><!-- .rn-option-page-tabs-list -->
</div><!-- .rn-option-page-tabs -->