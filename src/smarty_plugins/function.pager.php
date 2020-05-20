<?php
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {pager} function plugin
 * Type:     function<br>
 * Name:     pager<br>
 * Date:     Mar 25, 2016<br>
 * Purpose:  Create a pager for tables<br>
 * Examples: 
 * 		{pager
 * 			current_page = $page 
 * 			total_items = $totalItems
 *          items_per_page = $itemsPerPage
 *          name='myPager'
 *      }
 *
 * Params:
 * 		integer $current_page 	Current page. Default = 1
 * 		integer $total_items	Total quantity of items for pagination. Default = 0
 * 		integer $items_per_page	Quantity of items per page. Default = 10
 *      string  $name           Pager's name. Default = uniquid()
 *
 * @author  Leonardo Petrora for Equilibrium
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 */
function smarty_function_pager($params, $template)
{
    $page = isset($params['current_page'])? $params['current_page'] : 1;
    $total_items = isset($params['total_items'])? $params['total_items'] : 0;
    $items_per_page = isset($params['items_per_page'])? $params['items_per_page'] : 10;
    $name = isset($params['name'])? $params['name'] : uniqid();
	
    if ($total_items>0)
	{
	    $pages = ceil($total_items/$items_per_page);
		?>
		<div class="pagination" id="<?php echo $name;?>-Pagination">
		    <a href="#" class="first" data-action="first">&laquo;</a>
    		<a href="#" class="previous" data-action="previous">&lsaquo;</a>
		    <input type="text" readonly="readonly" />
		    <a href="#" class="next" data-action="next">&rsaquo;</a>
		    <a href="#" class="last" data-action="last">&raquo;</a>
		</div>
		<input type="hidden" name="<?php echo $name;?>-Page" id="<?php echo $name;?>-Page">
		<script>
		$('#<?php echo $name;?>-Pagination').jqPagination({
			current_page: <?php echo $page;?>,
			max_page: <?php echo $pages;?>,
			page_string: 'Página {current_page} de {max_page}',
    		paged: function(page) {
        		$('#<?php echo $name;?>-Page').val(page);
        		$('#<?php echo $name;?>-Page').closest("form").submit();
    		}
		});
		</script>
	<?php 
	}	
}