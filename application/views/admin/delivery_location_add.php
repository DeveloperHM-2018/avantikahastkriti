<div class="row">
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Location Name <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="location_name" required value="<?= $location_name ?>">
    </div>
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Latitude <span class="text-danger">*</span></label>
        <input class="form-control" type="number" name="latitude" required value="<?= $latitude ?>">
    </div>
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Longitude <span class="text-danger">*</span></label>
        <input class="form-control" type="number" name="longitude" required value="<?= $longitude ?>">
    </div>
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select">
            <option value="1" <?= $status == '1' ? 'selected' : '' ?>>Enable</option>
            <option value="0" <?= $status == '0' ? 'selected' : '' ?>>Disable</option>
        </select>
    </div>
</div>