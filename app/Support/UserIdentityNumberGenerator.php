<?php

namespace App\Support;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Str;

class UserIdentityNumberGenerator
{
    public function generate(?string $role, ?int $branchId, ?User $user = null): ?string
    {
        $roleCode = User::identityRoleCode($role);

        if ($roleCode === null) {
            return null;
        }

        $branchCode = $this->resolveBranchCode($role, $branchId);

        if ($branchCode === null) {
            return null;
        }

        $prefix = "{$roleCode}-{$branchCode}";

        if ($user && filled($user->identity_number) && str_starts_with((string) $user->identity_number, $prefix . '-')) {
            return $user->identity_number;
        }

        $sequence = $this->nextSequence($prefix, $user);

        return sprintf('%s-%03d', $prefix, $sequence);
    }

    private function resolveBranchCode(?string $role, ?int $branchId): ?string
    {
        if ($role === User::ROLE_KANTOR) {
            return 'PST';
        }

        if (! $branchId) {
            return null;
        }

        $branchCode = Branch::query()->whereKey($branchId)->value('code');

        if (! $branchCode) {
            return null;
        }

        $sanitized = Str::upper(preg_replace('/[^A-Za-z0-9]/', '', $branchCode) ?? '');

        return $sanitized !== '' ? $sanitized : null;
    }

    private function nextSequence(string $prefix, ?User $user = null): int
    {
        $query = User::query()->where('identity_number', 'like', $prefix . '-%');

        if ($user) {
            $query->whereKeyNot($user->id);
        }

        $maxSequence = $query->get(['identity_number'])
            ->map(function (User $user) {
                if (! preg_match('/-(\d+)$/', (string) $user->identity_number, $matches)) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter()
            ->max();

        return ((int) $maxSequence) + 1;
    }
}
