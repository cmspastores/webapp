<x-app-layout>
    <x-slot name="header">
        <h2 class="profile-header-title">{{ __('Profile') }}</h2>
    </x-slot>

    <div class="profile-page">

        {{-- Update Profile --}}
        <div class="profile-section">
            <section>
                <header>
                    <h2>Profile Information</h2>
                    <p>Update your account's profile information and email address.</p>
                </header>
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" class="profile-input" value="{{ old('name', $user->name) }}" required>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="profile-input" value="{{ old('email', $user->email) }}" required>
                    <button type="submit" class="profile-button profile-button-primary">Save</button>
                </form>
            </section>
        </div>

        {{-- Update Password --}}
        <div class="profile-section">
            <section>
                <header>
                    <h2>Update Password</h2>
                    <p>Ensure your account is using a long, random password to stay secure.</p>
                </header>
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <label for="current_password">Current Password</label>
                    <input id="current_password" name="current_password" type="password" class="profile-input" required>
                    <label for="password">New Password</label>
                    <input id="password" name="password" type="password" class="profile-input" required>
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="profile-input" required>
                    <button type="submit" class="profile-button profile-button-primary">Save</button>
                </form>
            </section>
        </div>

        {{-- Delete User --}}
        <div class="profile-section">
            <section>
                <header>
                    <h2>Delete Account</h2>
                    <p>Once your account is deleted, all of its resources and data will be permanently deleted. Please download any data you wish to retain.</p>
                </header>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <label for="delete_password">Confirm Password</label>
                    <input id="delete_password" name="password" type="password" class="profile-input" required>
                    <div style="margin-top:.5rem">
                        <button type="button" class="profile-button profile-button-secondary" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="profile-button profile-button-danger" style="margin-left:.5rem">Delete Account</button>
                    </div>
                </form>
            </section>
        </div>

    </div>
</x-app-layout>

<style>

/* Profile Header */    
.profile-header-title { font:900 28px 'Figtree',sans-serif; color:#5C3A21; line-height:1.2; text-align:center; text-shadow:2px 2px 6px rgba(0,0,0,0.25); letter-spacing:1.2px; text-transform:uppercase; margin-bottom:16px; position:relative; }
.profile-header-title::after { content:''; display:block; width:80px; height:4px; margin:8px auto 0; background:linear-gradient(90deg,#F4C38C,#E6A574); border-radius:2px; }
.dark .profile-header-title { color:#f6e7d8; }

/* Page & sections */
.profile-page { max-width:800px; margin:2rem auto; display:flex; flex-direction:column; gap:2rem; }
.profile-section { padding:2rem; background:#fff8f0; border-radius:1rem; border:1px solid #e6cbb3; box-shadow:0 4px 10px rgba(0,0,0,0.05); }
.dark .profile-section { background:#4b372d; border:1px solid #6e4f3a; }

/* Section headers */
.profile-section header h2 { font-size:1.25rem; font-weight:600; color:#5a3e2b; margin-bottom:.25rem; }
.dark .profile-section header h2 { color:#f6e7d8; }
.profile-section header p { font-size:.875rem; color:#7c5a44; margin-bottom:1rem; }
.dark .profile-section header p { color:#d5c2b3; }

/* Inputs */
.profile-input { width:100%; padding:.5rem .75rem; border:1px solid #e6cbb3; border-radius:.5rem; background:#fff8f0; color:#5a3e2b; margin:.25rem 0 .5rem; }
.dark .profile-input { background:#3b2b24; border:1px solid #6e4f3a; color:#f6e7d8; }

/* Buttons */
.profile-button { padding:.5rem 1rem; border-radius:.5rem; font-weight:600; cursor:pointer; transition:.2s; border:none; }
.profile-button-primary { background:#e59f71; color:#fff; }
.profile-button-primary:hover { background:#d5895c; }
.profile-button-secondary { background:#e6cbb3; color:#5a3e2b; }
.profile-button-secondary:hover { background:#d5b59a; }
.profile-button-danger { background:#d96e5f; color:#fff; }
.profile-button-danger:hover { background:#c65a4a; }
</style>
