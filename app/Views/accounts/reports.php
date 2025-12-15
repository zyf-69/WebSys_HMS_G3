<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Financial Reports</div>
        <div class="page-subtitle">Generate daily, monthly, yearly financial reports and tax reports.</div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Generate Report</div>
    </div>
    <form action="/WebSys_HMS_G3/accounts/reports/generate" method="post" style="padding: 20px;">
        <?= csrf_field() ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Report Type *</label>
                <select name="report_type" class="form-select" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="daily">Daily Report</option>
                    <option value="monthly">Monthly Report</option>
                    <option value="yearly">Yearly Report</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>
            <div id="monthField" style="display: none;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Month</label>
                <select name="month" class="form-select" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($i == date('n')) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div id="yearField">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Year</label>
                <input type="number" name="year" value="<?= date('Y') ?>" class="form-input" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div id="startDateField" style="display: none;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Start Date</label>
                <input type="date" name="start_date" class="form-input" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div id="endDateField" style="display: none;">
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">End Date</label>
                <input type="date" name="end_date" class="form-input" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
            </div>
            <div>
                <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px;">Format</label>
                <select name="format" class="form-select" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px;">
                    <option value="html">HTML (View)</option>
                    <option value="pdf" disabled>PDF (Coming Soon)</option>
                    <option value="excel" disabled>Excel (Coming Soon)</option>
                </select>
            </div>
        </div>
        <button type="submit" style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
            Generate Report
        </button>
    </form>
</div>

<script>
document.querySelector('select[name="report_type"]').addEventListener('change', function() {
    const reportType = this.value;
    const monthField = document.getElementById('monthField');
    const yearField = document.getElementById('yearField');
    const startDateField = document.getElementById('startDateField');
    const endDateField = document.getElementById('endDateField');
    
    if (reportType === 'daily') {
        monthField.style.display = 'none';
        yearField.style.display = 'block';
        startDateField.style.display = 'none';
        endDateField.style.display = 'none';
    } else if (reportType === 'monthly') {
        monthField.style.display = 'block';
        yearField.style.display = 'block';
        startDateField.style.display = 'none';
        endDateField.style.display = 'none';
    } else if (reportType === 'yearly') {
        monthField.style.display = 'none';
        yearField.style.display = 'block';
        startDateField.style.display = 'none';
        endDateField.style.display = 'none';
    } else if (reportType === 'custom') {
        monthField.style.display = 'none';
        yearField.style.display = 'none';
        startDateField.style.display = 'block';
        endDateField.style.display = 'block';
    }
});
</script>

<?= $this->endSection() ?>

