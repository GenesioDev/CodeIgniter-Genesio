<?php foreach($flash as $item => $value) : ?>
    <div class="alert alert-<?php echo $item; ?>">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?php echo $value; ?>
    </div>
<?php endforeach; ?>