<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<a href="<?php echo base_url('Excel_test/export'); ?>">Create Excel</a>

<?php echo form_open_multipart('Excel_test/import',array('name' => 'spreadsheet')); ?>
<table align="center" cellpadding = "5">
<tr>
<td>File :</td>
<td><input type="file" size="40px" name="upload_file" /></td>
<td class="error"><?php echo form_error('name'); ?></td>
<td colspan="5" align="center">
<input type="submit" value="Import Users"/></td>
</tr>
</table>
<?php echo form_close();?>

<?php $this->load->view('templates/footer'); ?>