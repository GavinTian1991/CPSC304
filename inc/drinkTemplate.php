<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <legend>Drink Information</legend>
        <div class="form-group">
            <label>Drink Name</label>
            <input type="text" name="drink_name" class="form-control" value="<?=htmlspecialchars($name);?>" required>
            <div class="text-danger"><?=$errors['name'];?></div>
        </div>
        <div class="form-group">
            <label>Drink Description (Optional)</label>
            <input type="text" name="drink_description" class="form-control" value="<?=htmlspecialchars($description);?>">
            <div class="text-danger"><?=$errors['description']; ?></div>
        </div>
        <div class="form-group">
            <label>Drink Price</label>
            <input type="text" name="drink_price" class="form-control" value="<?=htmlspecialchars($price);?>" required>
            <div class="text-danger"><?=$errors['price']; ?></div>
        </div>

        <div class="form-group">
            <label for="hotcold_select">Hot or Cold</label>
            <select name="drink_hot_or_cold" class="custom-select" id="hotcold_select">
                <option selected="" value="3">Both</option>
                <option value="2">Cold</option>
                <option value="1">Hot</option>
            </select>
        </div>
        <div class="form-group">
            <label>What is type name of this drink which you used in your shop?</label>
            <input type="text" name="drink_stype" class="form-control" value="<?=htmlspecialchars($stype)?>" required>
            <div class="text-danger"><?=$errors['stype'];?></div>
        </div>
        <div class="form-group">
            <?php
            $typesql = "SELECT * FROM shared_drink_types ORDER BY Type_Name";
            $typeresult = mysqli_query($conn, $typesql);
            $types = mysqli_fetch_all($typeresult, MYSQLI_ASSOC);
            mysqli_free_result($typeresult);
            ?>
            <label for="drink_type_select">Choose the type matches your drink the best.</label>
            <select name="drink_type" class="custom-select" id="drink_type_select">
                <option selected="" value="none">Type</option>
                <?php foreach($types as $type):?>
                    <option value="<?=$type['Type_ID']?>"><?=$type['Type_Name']?></option>
                <?php endforeach;?>
            </select>
        </div>
        <br>
        <button type="submit" name="save_drink" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_drink" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
