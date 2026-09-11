<x-auth.auth-layout title="Confirm Password" subtitle="This is a secure area of the application. Please confirm your password before continuing.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <x-auth.password-input
            name="password"
            label="Password"
            autocomplete="current-password"
            :required="true"
            :autofocus="true"
        />

        <x-auth.button>
            Confirm Password
        </x-auth.button>
    </form>
</x-auth.auth-layout>
