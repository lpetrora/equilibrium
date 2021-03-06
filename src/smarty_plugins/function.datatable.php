<?php
require_once __DIR__.'/function.csrf_token.php';
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {datatable} function plugin
 * Type:     function<br>
 * Name:     datatable<br>
 * Date:     Mar 25, 2016<br>
 * Purpose:  Display data generated by DataProvider<br>
 * Examples: 
 * 		{datatable 
 * 			data=$dataProvider 
 * 			rowNumbers = true
 * 			checkboxes = true
 * 			sort = true
 * 			filters = true
 * 			columns = ['Description'=>['Description'=>'Descripcion'], 
 * 						'IdFamily'=>['Description'=>'Familia'],
 * 						'ForClients' => ['Description'=>'Para clientes'], 
 * 						'Active' => ['Description' => 'Activa']]
 * 			valueMap = [ 'Active' => [ 1=> '<span class="label label-success">Si</label>', 0 => '<span class="label label-danger">No</label>'],
 * 						'IdFamily' => $family,
 * 						'ForClients' => [ 1=> '<span class="label label-success">Si</label>', 0 => '<span class="label label-danger">No</label>']
 * 						]
 * 			customToolbar = '<button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-ok"></i></button>
 * 							<button type="submit" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i></button>'
 * 			link = ['column' => 'Description', 'link'=>{make_url controller='Index' method='edit' arguments=['{Id}']}]
 * 		}
 * Params:
 * 		boolean $rowNumbers 	Show or hide row numbers (Default: false)
 * 		boolean $checkboxes		Show or hide checkboxes (Default: false)
 * 		boolean $sort			Show or hide sort buttons (Default: false)
 * 		boolean	$filters		Show or hide filter inputs (Default: false)
 * 		string	$tableClass 	Custom html table class (Default: 'table table-striped table-hover')
 * 		array	$data			Info generated by $datasource->getModels()
 * 		array	$columns		Array with columns info to show (Default: show all columns)
 * 		array	$valueMap		Array with column and an array of (value => newValue) (Default: no map)
 * 		string	$customToolbar	Buttons to append to internal toolbar (Default: none)
 * 		array	$link			Array with column name and link (recomend use {make_url}). Displays a link con column. Can use column names in URL as {column}
 * 		
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
function smarty_function_datatable($params, $template)
{
	$showRowNumbers = isset($params['rowNumbers'])? $params['rowNumbers'] : false;
	$showRowCheckBoxes = isset($params['checkboxes'])? $params['checkboxes'] : false;
	$showSort = isset($params['sort'])?$params['sort']:false;
	$tableClass = isset($params['tableClass'])? 'class="'.$params['tableClass']. '"':'class="table table-striped table-hover table-responsive"';
	$name = isset($params['data']['Name'])?$params['data']['Name']:'datatable'.rand();
	$valueMap = isset($params['valueMap'])?$params['valueMap']:[];
	$showFilters = isset ($params['filters'])?$params['filters']:false;
	$pkColumn = '';
	$customToolbar = isset($params['customToolbar'])?$params['customToolbar']:'';
	$link = isset($params['link'])?$params['link']:null;
	$linkColumn = isset($params['link']['column'])?$params['link']['column']:'';
	$nullValue = isset($params['nullValue'])?$params['nullValue']:'<span class="label label-default">Indefinido</span>';

	if (isset($params['pkColumn']))
	{
		$pkColumn = $params['pkColumn'];
	} else {
		$pkColumn = array_keys($params['data']['Columns']);
		$pkColumn = array_shift($pkColumn);
	}
	
	$columns = [];
	
	//Si no existe $params['data']['Models'] tirar excepcion;
	
	//Columnas
	if (isset($params['columns']))
	{
		foreach ($params['columns'] as $column => $info)
		{
			$col = isset($params['data']['Columns'][$column])?$col = $params['data']['Columns'][$column]:[];
			$col = array_merge($col, $info);			
			$columns[$column] = $col;
		}
		
	} else {
		$columns = $params['data']['Columns'];
	}
	
	$tmp = $columns;
	if ($showRowCheckBoxes) array_unshift($tmp, ['Type' => 'checkboxes', 'Description'=>'&nbsp;', 'Size'=>10, 'IsPk' => false]);
	if ($showRowNumbers) array_unshift($tmp, ['Type' => 'string', 'Description'=>'#', 'Size'=>10, 'IsPk' => false]);
	
	echo '<form id="Form_Datasource_'.$name.'" method="post">';
	
	if ($showFilters || ($customToolbar !== '')) 
	{
		echo '<div class="btn-toolbar pull-right"><div class="btn-group" role="toolbar">';
		echo $customToolbar;
		if ($showFilters) echo '<button type="button" class="btn btn-default" id="Button-'.$name.'-Search"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>';
		echo '</div></div>';
	}
	
	
	echo '<div id="Div_Datasource_'.$name.'">';
	echo "<table $tableClass><thead><tr>";
	foreach ($tmp as $idCol => $col) 
	{
		echo '<th>';
		if ($col['Type'] == 'checkboxes')
		{
			echo'<input type="checkbox" id="Check-'.$name.'-Check">'; 
		} else {
			echo $col['Description'];
			if ($showSort && isset($columns[$idCol]))
			{
				echo '&nbsp;';
				if (isset($params['data']['Sort'][$idCol]) && $params['data']['Sort'][$idCol] == 'ASC')
				{
					echo '<a href="#" class="Button-'.$name.'-Sort" data-column="'.$idCol.'"><span class="glyphicon glyphicon-sort-by-alphabet"></span></a>';
					echo '<input type="hidden" name="'.$name.'-Sort-'.$idCol.'" id="'.$name.'-Sort-'.$idCol.'" value="ASC" class="Field-'.$name.'-Sort">';
				} elseif (isset($params['data']['Sort'][$idCol]) && $params['data']['Sort'][$idCol] == 'DESC'){
					echo '<a href="#" class="Button-'.$name.'-Sort" data-column="'.$idCol.'"><span class="glyphicon glyphicon-sort-by-alphabet-alt"></span></a>';
					echo '<input type="hidden" name="'.$name.'-Sort-'.$idCol.'" id="'.$name.'-Sort-'.$idCol.'" value="DESC" class="Field-'.$name.'-Sort">';
				} else {
					echo '<a href="#" class="Button-'.$name.'-Sort" data-column="'.$idCol.'"><span class="glyphicon glyphicon-sort"></span></a>';
					echo '<input type="hidden" name="'.$name.'-Sort-'.$idCol.'" id="'.$name.'-Sort-'.$idCol.'" value="" class="Field-'.$name.'-Sort">';
				}
			}
		}
		echo '</th>';
	}
	echo '</tr>';
	if ($showFilters)
	{
		echo '<tr>';
		if ($showRowCheckBoxes) echo '<td>&nbsp;</td>';
		if ($showRowNumbers) echo '<td>&nbsp;</td>';
		foreach ($columns as $column =>$info)
		{
			echo '<td>';
			$columnName = $name.'-Search-'.$column;
			if (!isset($valueMap[$column]))
			{
				$value = isset($params['data']['Search'][$column])?$params['data']['Search'][$column]:'';
				switch ($info['Type']) {
					case 'integer':
						echo '<input type="number" name="'.$columnName.'" class="form-control" >';
					;
					break;
					
					default:
						echo '<input type="text" name="'.$columnName.'" class="form-control" value="'.$value.'">';
						;
					break;
				}
			} else {
				echo '<select name="'.$columnName.'" class="form-control">';
				echo '<option value=""></option>';
				foreach ($valueMap[$column] as $value => $text)
				{
					$selected = '';
					if (isset($params['data']['Search'][$column]) && ($value == $params['data']['Search'][$column])) $selected = 'selected = "selected" ';
					echo '<option value="'.$value.'" '. $selected .' >'.$text.'</option>';
				}
				echo '</select>';
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	
	echo '</thead><tbody>';
	$i = 1;
	if (!empty($params['data']['Models']))
	{
		$is_array = is_array($params['data']['Models']);
		if ($is_array)
        {
            foreach ($params['data']['Models'] as $model)
            {
                echo "<tr>";
                if ($showRowNumbers)
                {
                    echo "<td>$i</td>";
                }
                if ($showRowCheckBoxes)
                {
                    echo '<td><input type="checkbox" name="'.$name.'-'.$pkColumn .'[]" value="'.$model[$pkColumn].'"></td>';
                }
                
                foreach ($columns as $col => $info)
                {
                    $value = $model[$col];
                    if ($value === null)
                    {
                        $value = $nullValue;
                    } else {
                        if ($info['Type'] == 'TIMESTAMP') $value = date('d/m/Y H:i:s', strtotime($value));
                        if (($info['Type'] == 'BOOLEAN') && !isset($valueMap[$col])) $value = ($value == 1)? 'Si' : 'No';
                        if (isset($valueMap[$col][$value])) $value = $valueMap[$col][$value];
                    }
                    echo '<td>';
                    if ($col == $linkColumn)
                    {
                        $href = $link['link'];
                        foreach ($model as $cc => $cv)
                        {
                            if (is_array($cv)) continue;
                            $href = str_replace('{'.$cc.'}', $cv, $href);
                        }
                        echo '<a href="'.$href.'">'.$value.'</a>';
                    } else {
                        echo $value;
                    }
                    echo '</td>';
                }
                $i++;
            }
	    } else {
	        foreach ($params['data']['Models'] as $model)
	        {
	            echo "<tr>";
	            if ($showRowNumbers)
	            {
	                echo "<td>$i</td>";
	            }
	            if ($showRowCheckBoxes)
	            {
	                $value = $model->{'get'.$pkColumn}();
	                echo '<td><input type="checkbox" name="'.$name.'-'.$pkColumn .'[]" value="'.$value.'"></td>';
	            }
	            
	            foreach ($columns as $col => $info)
	            {
	                $value = $model->{'get'.$col}();
	                if ($value === null)
	                {
	                    $value = $nullValue;
	                } else {
	                    if ($info['Type'] == 'TIMESTAMP') $value = $value->format('d-m-Y H:i:s');
	                    if (($info['Type'] == 'BOOLEAN') && !isset($valueMap[$col])) $value = ($value == 1)? 'Si' : 'No';
	                    if (isset($valueMap[$col][$value])) $value = $valueMap[$col][$value];
	                }
	                echo '<td>';
	                if ($col == $linkColumn)
	                {
	                    $href = $link['link'];
	                    foreach ($model->toArray() as $cc => $cv)
	                    {
	                        if (is_array($cv)) continue;
	                        $href = str_replace('{'.$cc.'}', $cv, $href);
	                    }
	                    echo '<a href="'.$href.'">'.$value.'</a>';
	                } else {
	                    echo $value;
	                }
	                echo '</td>';
	            }
	            
	            $i++;
	        }
	    }

	} else {
		$colspan = count($columns);
		if ($showRowCheckBoxes) $colspan++;
		if ($showRowNumbers) $colspan++;
		
		echo "<tr><td colspan=\"$colspan\" class=\"alert alert-warning\" align=\"center\">No hay elementos para mostrar</td></tr>";
	}
	echo '</tbody></table>';
	
	try {
		echo smarty_function_csrf_token([], $template);		
	} catch (Exception $e) {
	}
		
	
	if (($params['data']['PageSize'] !== null)&&($params['data']['TotalModels']>0))
	{
		$pages = ceil($params['data']['TotalModels']/$params['data']['PageSize']);
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
			current_page: <?php echo $params['data']['Page'];?>,
			max_page: <?php echo $pages;?>,
			page_string: 'Página {current_page} de {max_page}',
    		paged: function(page) {
        		$('#<?php echo $name;?>-Page').val(page);
        		$('#Form_Datasource_<?php echo $name;?>').submit();
    		}
		});
		</script>
	<?php 
	}	
	?>
	<script>
		$('#Button-<?php echo $name;?>-Search').click(function(){
       		$('#<?php echo $name;?>-Page').val(1);
       		$('#Form_Datasource_<?php echo $name;?>').submit();
		});
	</script>
	<script>
		$('.Button-<?php echo $name;?>-Sort').click(function(){
			var column = $(this).attr('data-column');
			var currentValue = $('#<?php echo $name;?>-Sort-' + column).val();
			var nextValue = '';
			switch (currentValue) {
			case 'ASC':
				nextValue = 'DESC';
				break;
			case 'DESC':
				nextValue = '';
				break;

			default:
				nextValue = 'ASC';
				break;
			}
			$('.Field-<?php echo $name;?>-Sort').each(function(){$(this).val('')});
			$('#<?php echo $name;?>-Sort-' + column).val(nextValue);
    		$('#Form_Datasource_<?php echo $name;?>').submit();
		});
	</script>
	<script>
		$("#Check-<?php echo $name;?>-Check").click(function(){
			var checked = $(this).prop('checked');
			$('input[name="<?php echo $name;?>-<?php echo $pkColumn;?>[]"]').each(function(){$(this).prop('checked', checked)});
		});
	</script>
	</div>
	</form>
	<?php
}