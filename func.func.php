<?php

/**
 * Some usefull functions for application
 *
 * @copyright	http://www.mekdrop.name
 * @license		http://www.opensource.org/licenses/lgpl-3.0.html
 * @package     main
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 * @version     $Id$
 */

function show_form(&$fields, &$data, $button = 'Save', $action = 'save') {
   global $objBipConfigManager
?>
<form method="post">
  <table border="0">
	 <?php foreach ($fields as $fname => $ftype) { ?>
	 <tr>
		<td title="<?php echo htmlentities($objBipConfigManager->getVarDesc($fname)); ?>"><?php echo ucfirst(str_replace('_',' ',$fname));?>:</td>
		<td>
			<?php
				switch ($ftype) {
					case 'bool':
						?>
						<input type="checkbox" name="<?php echo $fname;?>" value="true" onclick="this.className=this.checked?'checked':'';"<?php if ($data[$fname]) echo ' checked="checked" class="checked"'; if ($fname == 'admin' && !$_SESSION['is_admin']) echo " disabled=\"disabled\""; ?> />
						<?php
					break;
					case 'other':
						?>
						<input type="text" name="<?php echo $fname;?>" value="<?php echo $data[$fname]; ?>" />
						<?php
					break;
					case 'password':
						?>
						<input type="password" name="<?php echo $fname;?>[]" value="" maxlength="8" size="10"/> (enter password; min 4 chars; max 8 chars)
						<br />
						<input type="password" name="<?php echo $fname;?>[]" value="" maxlength="8" size="10"/> (repeat password)
						<?php
					break;
					case 'password_sys':
						?>
						<input type="password" name="<?php echo $fname;?>[]" value="" maxlength="8" size="10"/> (enter password; min 4 chars; max 8 chars)
						<br />
						<input type="password" name="<?php echo $fname;?>[]" value="" maxlength="8" size="10"/> (repeat password)
						<br />
						<input type="text" name="<?php echo $fname;?>[]" value="<?php echo $data[$fname]; ?>" maxlength="50" size="50"/> (with bipmkpw generated hash)
						<?php
					break;
					default:
						?>
						<select name="<?php echo $fname;?>">
							<?php foreach ($ftype as $k => $v) { ?>
							<option value="<?php echo $k;?>"<?php if ($data[$fname] == $k) echo ' selected="selected"'  ?>><?php echo $v;?></option>
							<?php } ?>
						</select>
						<?php
					break;
				}
			?>
		</td>
	 </tr>
	 <?php } ?>
	 <tr>
		<td></td><td><input type="hidden" name="action" value="<?php echo $action;?>" /><button type="submit"><?php echo $button;?></button></td>
	 </tr>
  </table>  
</form> <?php

}

function action_button($action, $button_name) {
	?>
	<form method="post" class="action">
		<input type="hidden" name="action" value="<?php echo $action;?>" />
		<button type="submit"><?php echo $button_name;?></button>
	</form>
	<?php
}

function show_list($url, $list, $show_delete_url = '') {	
	if (count($list) < 1) {
		echo '<i>Empty list</i>';
		return;
	}
	?>
	<ul class="list" type="square">
		<?php foreach ($list as $id => $item) { ?>
			<li>
				<?php if ($show_delete_url) { ?>
				<a href="<?php echo "$show_delete_url&item_id=$id";?>" class="delete">[x]</a>
				<?php } ?>
				<a href="<?php echo "$url&item_id=$id";?>" class="name"><?php echo $item;?></a>
			</li>
		<?php } ?>
	</ul>
	<?php
}

function br($how_many = 1) {
	echo nl2br(str_repeat("\n", $how_many));
}

function auto_access_denied() {
	if (!$_SESSION['is_admin']) {
		echo 'Access diened';
		return true;
	}
	return false;
}

?>