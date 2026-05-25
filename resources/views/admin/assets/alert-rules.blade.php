@extends('layouts.admin')

@section('title', 'Alert Rules — ' . $asset->name)
@section('portal_label', 'Admin Portal')
@section('page_title', 'Alert Rules')
@section('dashboard_url', route('admin.dashboard'))

@section('content')

<div class="page-heading">
    <div>
        <p class="page-eyebrow">Asset: {{ $asset->asset_code }}</p>
        <h1>Custom Alert Rules</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.assets.index') }}">Assets</a> /
            <span>Alert Rules</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button"
                class="btn btn-primary"
                data-modal-open="createRuleModal">
            + Add Rule
        </button>
        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline">
            Back to Assets
        </a>
    </div>
</div>

{{-- ASSET INFO CARD --}}
<div class="content-card" style="margin-bottom: 1.5rem;">
    <div class="detail-grid">
        <div class="detail-item">
            <span>Asset Name</span>
            <strong>{{ $asset->name }}</strong>
        </div>
        <div class="detail-item">
            <span>Asset Code</span>
            <strong>{{ $asset->asset_code }}</strong>
        </div>
        <div class="detail-item">
            <span>Department</span>
            <strong>{{ $asset->department->name ?? '—' }}</strong>
        </div>
        <div class="detail-item">
            <span>Status</span>
            <strong>
                <span class="badge {{ $asset->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                    {{ ucfirst($asset->status) }}
                </span>
            </strong>
        </div>
    </div>
</div>

{{-- RULES TABLE --}}
<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Configured Rules</h2>
            <p>Rules trigger alerts automatically when conditions are met for this asset.</p>
        </div>
    </div>

    @if($rules->isEmpty())
        <x-empty-state
            icon="alert"
            title="No Alert Rules Yet"
            description="Add a custom rule to automatically trigger alerts for this asset."
            action="#"
            actionText="Add First Rule"
        />
    @else
        <div class="table-wrap">
            <table class="app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Rule Name</th>
                        <th>Type</th>
                        <th>Threshold</th>
                        <th>Action</th>
                        <th>Recipients</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rules as $rule)
                    <tr>
                        <td><strong>{{ $rule->rule_name }}</strong></td>
                        <td>
                            <span class="badge badge-soft">
                                {{ str_replace('_', ' ', ucfirst($rule->rule_type)) }}
                            </span>
                        </td>
                        <td>{{ $rule->threshold_value ?? '—' }}</td>
                        <td>
                            <span class="badge badge-soft">
                                {{ ucfirst($rule->action) }}
                            </span>
                        </td>
                        <td>
                            @if($rule->recipient_emails && count($rule->recipient_emails) > 0)
                                <span class="badge badge-soft">
                                    {{ count($rule->recipient_emails) }} email(s)
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $rule->is_active ? 'badge-success' : 'badge-warning' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="inline-actions">
                            {{-- Toggle active status --}}
                            <form method="POST"
                                  action="{{ route('admin.assets.rules.toggle', ['asset' => $asset->id, 'rule' => $rule->id]) }}"
                                  style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="btn btn-outline btn-sm">
                                    {{ $rule->is_active ? 'Disable' : 'Enable' }}
                                </button>
                            </form>

                            {{-- Edit rule --}}
                            <button type="button"
                                    class="btn btn-outline btn-sm edit-rule-btn"
                                    data-rule-id="{{ $rule->id }}"
                                    data-rule-name="{{ $rule->rule_name }}"
                                    data-rule-type="{{ $rule->rule_type }}"
                                    data-threshold="{{ $rule->threshold_value }}"
                                    data-action="{{ $rule->action }}"
                                    data-emails="{{ implode(',', $rule->recipient_emails ?? []) }}"
                                    data-phones="{{ implode(',', $rule->recipient_phones ?? []) }}">
                                Edit
                            </button>

                            {{-- Delete rule --}}
                            <form method="POST"
                                  action="{{ route('admin.assets.rules.destroy', ['asset' => $asset->id, 'rule' => $rule->id]) }}"
                                  class="js-confirm-delete"
                                  data-title="Delete this rule?"
                                  data-text="Rule '{{ $rule->rule_name }}' will be permanently removed."
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- CREATE RULE MODAL --}}
<div id="createRuleModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST"
              action="{{ route('admin.assets.rules.store', $asset) }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h2>Create Alert Rule</h2>
                    <p>Define a condition that will automatically trigger an alert.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Rule Name *</label>
                        <input type="text"
                               name="rule_name"
                               placeholder="e.g. Speed Limit Alert"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Rule Type *</label>
                        <select name="rule_type" required>
                            <option value="">Select type</option>
                            <option value="speed_threshold">Speed Threshold</option>
                            <option value="geofence_breach">Geofence Breach</option>
                            <option value="inactivity">Inactivity</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Threshold Value</label>
                        <input type="number"
                               name="threshold_value"
                               step="0.01"
                               placeholder="e.g. 100 for 100 km/h">
                    </div>

                    <div class="form-group">
                        <label>Action *</label>
                        <select name="action" id="create_action" required>
                            <option value="">Select action</option>
                            <option value="email">Send Email</option>
                            <option value="sms">Send SMS</option>
                            <option value="push">Push Notification</option>
                            <option value="database">Database Only</option>
                        </select>
                    </div>

                    <div class="form-group field-span-2"
                         id="create_email_field"
                         style="display:none;">
                        <label>Recipient Emails</label>
                        <input type="text"
                               name="recipient_emails_raw"
                               placeholder="Comma separated: manager@example.com, admin@example.com">
                        <small style="color: var(--muted);">
                            Separate multiple emails with commas
                        </small>
                    </div>

                    <div class="form-group field-span-2"
                         id="create_phone_field"
                         style="display:none;">
                        <label>Recipient Phone Numbers</label>
                        <input type="text"
                               name="recipient_phones_raw"
                               placeholder="Comma separated: +255712345678, +255787654321">
                        <small style="color: var(--muted);">
                            Include country code, separate with commas
                        </small>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Save Rule
                </button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT RULE MODAL --}}
<div id="editRuleModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" id="editRuleForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <div>
                    <h2>Edit Alert Rule</h2>
                    <p>Update the rule conditions and actions.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>

            <div class="modal-body">
                <div class="form-grid">

                    <div class="form-group">
                        <label>Rule Name *</label>
                        <input type="text"
                               name="rule_name"
                               id="edit_rule_name"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Rule Type *</label>
                        <select name="rule_type" id="edit_rule_type" required>
                            <option value="speed_threshold">Speed Threshold</option>
                            <option value="geofence_breach">Geofence Breach</option>
                            <option value="inactivity">Inactivity</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Threshold Value</label>
                        <input type="number"
                               name="threshold_value"
                               id="edit_threshold"
                               step="0.01">
                    </div>

                    <div class="form-group">
                        <label>Action *</label>
                        <select name="action"
                                id="edit_action"
                                required>
                            <option value="email">Send Email</option>
                            <option value="sms">Send SMS</option>
                            <option value="push">Push Notification</option>
                            <option value="database">Database Only</option>
                        </select>
                    </div>

                    <div class="form-group field-span-2">
                        <label>Recipient Emails</label>
                        <input type="text"
                               name="recipient_emails_raw"
                               id="edit_emails"
                               placeholder="Comma separated emails">
                    </div>

                    <div class="form-group field-span-2">
                        <label>Recipient Phone Numbers</label>
                        <input type="text"
                               name="recipient_phones_raw"
                               id="edit_phones"
                               placeholder="Comma separated phone numbers">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Update Rule
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Show/hide email and phone fields based on action selection (create modal)
    const createAction = document.getElementById('create_action');
    const createEmailField = document.getElementById('create_email_field');
    const createPhoneField = document.getElementById('create_phone_field');

    if (createAction) {
        createAction.addEventListener('change', function () {
            createEmailField.style.display = this.value === 'email' ? 'block' : 'none';
            createPhoneField.style.display = this.value === 'sms' ? 'block' : 'none';
        });
    }

    // Populate edit modal when Edit button clicked
    document.querySelectorAll('.edit-rule-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const ruleId   = this.dataset.ruleId;
            const assetId  = {{ $asset->id }};
            const form     = document.getElementById('editRuleForm');

            form.action = '/admin/assets/' + assetId + '/rules/' + ruleId;

            document.getElementById('edit_rule_name').value  = this.dataset.ruleName  || '';
            document.getElementById('edit_rule_type').value  = this.dataset.ruleType  || '';
            document.getElementById('edit_threshold').value  = this.dataset.threshold || '';
            document.getElementById('edit_action').value     = this.dataset.action    || '';
            document.getElementById('edit_emails').value     = this.dataset.emails    || '';
            document.getElementById('edit_phones').value     = this.dataset.phones    || '';

            document.getElementById('editRuleModal').classList.add('is-open');
            document.body.classList.add('modal-open');
        });
    });

});
</script>
@endpush
