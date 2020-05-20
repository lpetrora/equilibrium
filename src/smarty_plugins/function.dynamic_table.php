<?php
require_once __DIR__.'/function.csrf_token.php';
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {dynamic_table} function plugin
 * Type:     function<br>
 * Name:     dynamic_table<br>
 * Date:     Mar 25, 2016<br>
 * Purpose:  Render table in witch add rows or delete are easy<br>
 * Examples: 
 * 
 * @author  Leonardo Petrora for Equilibrium
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @throws SmartyException
 * @return string
 * 
 */
function smarty_function_dynamic_table($params, $template)
{
	if (!isset($params['columns'])) throw new SmartyException('columns attribute missing');
	
	$name = isset($params['name'])?$params['name']:'dynamic_table_' . rand(1,1000);
	$min = isset($params['min'])?$params['min']:1;
	$max = isset($params['max'])?$params['max']:null;
	$tableClass = isset($params['tableClass'])? 'class="'.$params['tableClass']. '"':'class="table table-striped table-hover"';
	$captionAdd = isset($params['captionAdd'])? $params['captionAdd']:'Nuevo elemento';
	$captionRemove = isset($params['captionRemove'])? $params['captionRemove']:'Eliminar elemento';
	$captionUnableToRemove = isset($params['captionRemove'])? $params['captionRemove']:'No se puede eliminar esta fila';
	
	$columns = $params['columns'];
	$values = isset($params['values'])?$params['values']:[];
	if (empty($values)) $values = [null];
	$visibleColumns = 0;
	
	echo "<table $tableClass id=\"$name\"><thead><tr>";
	foreach ($columns as $column)
	{
		if (isset($column['visible']) && ($column['visible'] == false))
		{
			echo '<th style="display: none;">';
		} else {
			$visibleColumns++;
			echo '<th>';
		}
		echo $column['title'] . '</th>'; 
	}
	$visibleColumns++;
	echo "<th>&nbsp;</th></tr></thead><tbody>";
	
	foreach ($values as $value)
	{
		echo '<tr>';
		foreach ($columns as $colName => $column)
		{
			$field = isset($column['valueField'])?$column['valueField']:$colName;
			$invisible = isset($column['visible']) && ($column['visible'] == false);
			if ($invisible) $invisible = ' style="display: none;"';
			
			echo '<td '. $invisible .'>';
			echo '<input type="' . $column['type'] . '" class="form-control" name="' . $colName . '[]"';			
			if (isset($value[$field])) echo ' value="'. $value[$field].'"';			
			echo '>';
			echo '</td>';
		}
		echo '<td class="text-right">';
		echo '<button type="button" class="btn btn-default YS-REMOVE-ROW" title="'. $captionRemove . '"><span class="glyphicon glyphicon-minus"></span></button>';
		echo '</td>';
		
		echo '</tr>';
	}
		
	echo "</tbody><tfoot>";
	echo '<tr><td colspan="' .$visibleColumns . '" class="text-right">';
	echo '<button type="button" class="btn btn-default YS-INSERT-ROW" title="'. $captionAdd . '" data-table-id="'.$name.'"><span class="glyphicon glyphicon-plus"></span></button>';
	echo "</td></tr></tfoot></table>";
	?>
	<script>
	$(document).on('click', '.YS-REMOVE-ROW', function() {
		var body = $(this).closest('tbody');
    	if ( $(body).children().length == 1)        	
    	{
            alert ('No se puede eliminar este registro');
            return false;
    	}
    	$(this).closest('tr').remove();
	});
	</script>
	<script>
	$('.YS-INSERT-ROW').click(function(){
		var table_id = $(this).attr('data-table-id');
		var body = $('#'+table_id).find('tbody');
		var contenido = $(body).children().first().html();
		$(body).append('<tr>' + contenido + '</tr>');

		$(body).children().last().find('input').each(function(){
			$(this).val('');
		});
		$(body).children().last().find('select').each(function(){
			$(this).val('');
		});
	});
	</script>
	<?php
}