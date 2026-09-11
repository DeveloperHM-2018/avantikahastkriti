<div class="row">
    <div class="col-lg-12 mb-3">
        <label class="col-form-label">Question <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="question" required value="<?= htmlspecialchars($question ?? '') ?>">
    </div>
    <div class="col-lg-12 mb-3">
        <label class="col-form-label">Answer <span class="text-danger">*</span></label>
        <textarea class="form-control" name="answer" rows="5" required><?= $answer ?? '' ?></textarea>
        <small class="text-muted">You can use <code>{min_amount}</code> and <code>{policy_url}</code> as placeholders, and basic HTML tags like &lt;b&gt; or &lt;a&gt;.</small>
    </div>
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Display Order</label>
        <input class="form-control" type="number" name="sort_order" value="<?= (int) ($sort_order ?? 0) ?>">
    </div>
    <div class="col-lg-6 mb-3">
        <label class="col-form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select">
            <option value="1" <?= ($status ?? '1') == '1' ? 'selected' : '' ?>>Enable</option>
            <option value="0" <?= ($status ?? '1') == '0' ? 'selected' : '' ?>>Disable</option>
        </select>
    </div>
</div>
