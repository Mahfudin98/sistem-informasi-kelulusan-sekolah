<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Validator
 *
 * Laravel-inspired validation with a fluent rule definition.
 *
 * Example:
 *   $v = Validator::make($request->all(), [
 *       'name'  => 'required|min:3|max:100',
 *       'email' => 'required|email',
 *       'nisn'  => 'required|numeric|length:10',
 *   ]);
 *
 *   if ($v->fails()) { ... $v->errors() ... }
 */
final class Validator
{
    private array $errors = [];

    private function __construct(
        private readonly array $data,
        private readonly array $rules,
        private readonly array $messages = [],
    ) {}

    public static function make(array $data, array $rules, array $messages = []): self
    {
        $instance = new self($data, $rules, $messages);
        $instance->validate();
        return $instance;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $this->applyRule($field, $value, $ruleName, $param);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, ?string $param): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        $failed = match ($rule) {
            'required' => $value === null || trim((string) $value) === '',
            'email'    => !filter_var($value, FILTER_VALIDATE_EMAIL),
            'numeric'  => !is_numeric($value),
            'integer'  => !filter_var($value, FILTER_VALIDATE_INT),
            'alpha'    => !ctype_alpha((string) $value),
            'alphanumeric' => !ctype_alnum((string) $value),
            'url'      => !filter_var($value, FILTER_VALIDATE_URL),
            'min'      => strlen((string) $value) < (int) $param,
            'max'      => strlen((string) $value) > (int) $param,
            'length'   => strlen((string) $value) !== (int) $param,
            'min_val'  => (float) $value < (float) $param,
            'max_val'  => (float) $value > (float) $param,
            'in'       => !in_array($value, explode(',', (string) $param), true),
            'not_in'   => in_array($value, explode(',', (string) $param), true),
            'confirmed' => $value !== ($this->data[$field . '_confirmation'] ?? null),
            'date'     => strtotime((string) $value) === false,
            default    => false,
        };

        if (!$failed) {
            return;
        }

        $customKey = "{$field}.{$rule}";
        $message   = $this->messages[$customKey] ?? $this->defaultMessage($label, $rule, $param);

        $this->errors[$field][] = $message;
    }

    private function defaultMessage(string $label, string $rule, ?string $param): string
    {
        return match ($rule) {
            'required'     => "{$label} wajib diisi.",
            'email'        => "{$label} harus berupa alamat email yang valid.",
            'numeric'      => "{$label} harus berupa angka.",
            'integer'      => "{$label} harus berupa bilangan bulat.",
            'alpha'        => "{$label} hanya boleh berisi huruf.",
            'alphanumeric' => "{$label} hanya boleh berisi huruf dan angka.",
            'url'          => "{$label} harus berupa URL yang valid.",
            'min'          => "{$label} minimal {$param} karakter.",
            'max'          => "{$label} maksimal {$param} karakter.",
            'length'       => "{$label} harus tepat {$param} karakter.",
            'min_val'      => "{$label} minimal bernilai {$param}.",
            'max_val'      => "{$label} maksimal bernilai {$param}.",
            'in'           => "{$label} tidak valid.",
            'not_in'       => "{$label} tidak diperbolehkan.",
            'confirmed'    => "{$label} konfirmasi tidak cocok.",
            'date'         => "{$label} harus berupa tanggal yang valid.",
            default        => "{$label} tidak valid.",
        };
    }
}
