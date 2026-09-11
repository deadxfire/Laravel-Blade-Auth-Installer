<x-auth.auth-layout title="Reset Password" subtitle="Choose a strong, unique password for your account.">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-auth.input
            type="email"
            name="email"
            label="Email Address"
            :value="old('email', $request->email)"
            autocomplete="username"
            :required="true"
            :autofocus="true"
        />

        <x-auth.password-input
            name="password"
            label="New Password"
            autocomplete="new-password"
            placeholder="Minimum 8 characters"
            :required="true"
        />

        <x-auth.password-input
            name="password_confirmation"
            label="Confirm New Password"
            autocomplete="new-password"
            placeholder="Repeat new password"
            :required="true"
        />

        <x-auth.button>
            Reset Password
        </x-auth.button>
    </form>
</x-auth.auth-layout>
