<style>
    .password-toggle-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }
    .password-toggle-icon:hover {
        color: #495057;
    }
    .position-relative {
        position: relative;
    }
    .form-group .position-relative .form-control {
        padding-right: 40px;
    }
</style>

<form id="reset-password-form" class="row g-4 align-items-center">
    <input type="hidden" name="email" value="{{ $email }}">

    <div class="col-12">
        <div class="form-group">
            <label class="label-cl">New Password</label>
            <div class="position-relative">
                <input type="password" name="password" class="form-control" placeholder="Enter new password" id="new_password" required minlength="6">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('new_password', this)"></i>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label class="label-cl">Confirm Password</label>
            <div class="position-relative">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" id="confirm_password" required minlength="6">
                <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('confirm_password', this)"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <button type="submit" class="btn btn-success rounded-3 px-5">Reset Password</button>
    </div>
</form>

<script>
function togglePassword(fieldId, iconElement) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        iconElement.classList.remove('fa-eye');
        iconElement.classList.add('fa-eye-slash');
    } else {
        field.type = "password";
        iconElement.classList.remove('fa-eye-slash');
        iconElement.classList.add('fa-eye');
    }
}
</script>