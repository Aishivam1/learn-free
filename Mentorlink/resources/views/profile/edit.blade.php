@extends('layouts.app')

@section('content')
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="compact-profile-form">
            <h2 class="form-title">Edit Profile</h2>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                </div>

                <div class="form-row">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" disabled>
                </div>

                <div class="form-row">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" placeholder="Leave blank to keep current">
                </div>

                <div class="form-row">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="2">{{ old('bio', auth()->user()->bio) }}</textarea>
                </div>

                <div class="form-row avatar-row">
                    <label>Avatar</label>
                    <div class="avatar-selector">
                        <button type="button" id="avatarDropdownBtn">
                            <img id="selectedAvatar" src="{{ asset('avatar/' . auth()->user()->avatar) }}" alt="Avatar">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        
                        <div class="avatar-panel" id="avatarOptions">
                            <div class="avatar-grid">
                                @foreach ($avatars as $avatar)
                                    <div class="avatar-item {{ auth()->user()->avatar == $avatar ? 'selected' : '' }}"
                                        data-avatar="{{ $avatar }}">
                                        <img src="{{ asset('avatar/' . $avatar) }}" alt="Avatar Option">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="avatar" id="selectedAvatarInput" value="{{ auth()->user()->avatar }}">
                    </div>
                </div>

                <button type="submit" class="submit-btn">Update Profile</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdownBtn = document.getElementById("avatarDropdownBtn");
        const dropdownPanel = document.getElementById("avatarOptions");
        const selectedAvatarImg = document.getElementById("selectedAvatar");
        const selectedAvatarInput = document.getElementById("selectedAvatarInput");
        const profileForm = document.querySelector(".compact-profile-form");

        dropdownBtn.addEventListener("click", function(e) {
            e.preventDefault();
            dropdownPanel.style.display = dropdownPanel.style.display === "block" ? "none" : "block";
        });

        document.querySelectorAll(".avatar-item").forEach(item => {
            item.addEventListener("click", function() {
                document.querySelectorAll(".avatar-item").forEach(el => {
                    el.classList.remove("selected");
                });
                
                this.classList.add("selected");
                
                const selectedAvatar = this.getAttribute("data-avatar");
                selectedAvatarImg.src = "/avatar/" + selectedAvatar;
                selectedAvatarInput.value = selectedAvatar;
                dropdownPanel.style.display = "none";
            });
        });

        document.addEventListener("click", function(event) {
            if (!dropdownBtn.contains(event.target) && !dropdownPanel.contains(event.target)) {
                dropdownPanel.style.display = "none";
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
/* Compact Form Container */
.compact-profile-form {
    width: 90%;
    max-width: 450px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    position: relative;
    max-height: 95vh;
}

/* Form Title */
.form-title {
    font-size: 1.3rem;
    color: #2563eb;
    text-align: center;
    margin-bottom: 1.25rem;
    font-weight: 600;
}

/* Form Row Layout */
.form-row {
    margin-bottom: 1rem;
}

.form-row label {
    display: block;
    font-weight: 500;
    font-size: 0.85rem;
    color: #4b5563;
    margin-bottom: 0.3rem;
}

.form-row input,
.form-row textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.9rem;
    background-color: #f9fafb;
}

.form-row input:focus,
.form-row textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
    outline: none;
}

/* Avatar Selector */
.avatar-row {
    display: flex;
    flex-direction: column;
}

.avatar-selector {
    position: relative;
}

#avatarDropdownBtn {
    display: flex;
    align-items: center;
    padding: 0.4rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
}

#avatarDropdownBtn img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

#avatarDropdownBtn i {
    margin-left: auto;
    font-size: 0.75rem;
    color: #6b7280;
}

/* Avatar Panel */
.avatar-panel {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    width: 100%;
    max-height: 160px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    display: none;
    z-index: 100;
    padding: 0.5rem;
}

.avatar-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}

.avatar-item {
    display: flex;
    justify-content: center;
    cursor: pointer;
    padding: 3px;
    border-radius: 50%;
}

.avatar-item img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid transparent;
}

.avatar-item:hover img {
    border-color: #2563eb;
}

.avatar-item.selected img {
    border-color: #2563eb;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background-color: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.625rem;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
}

.submit-btn:hover {
    background-color: #1d4ed8;
}

/* Scrollbar Styling - Only for Avatar Panel */
.avatar-panel::-webkit-scrollbar {
    width: 4px;
}

.avatar-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.avatar-panel::-webkit-scrollbar-thumb {
    background: #c5c7d0;
    border-radius: 10px;
}

/* Mobile Responsiveness */
@media (max-width: 480px) {
    .compact-profile-form {
        width: 95%;
        padding: 1.25rem;
    }
    
    .avatar-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
@endpush