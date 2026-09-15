<?php

namespace App\Services\Api;

use App\Models\UsersModel;
use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Hash;

class UsersApiService
{
    protected UsersRepository $usersRepository;
    protected CompaniesRepository $companiesRepository;
    protected EmployeesRepository $employeesRepository;

    public function __construct(
        UsersRepository $usersRepository,
        CompaniesRepository $companiesRepository,
        EmployeesRepository $employeesRepository
    ) {
        $this->usersRepository = $usersRepository;
        $this->companiesRepository = $companiesRepository;
        $this->employeesRepository = $employeesRepository;
    }

    /**
     * Login user, company or employee
     * and generate a Sanctum token.
     */
    public function checkLogin(array $credentials): array
    {
        /*
        |--------------------------------------------------------------------------
        | USER LOGIN
        |--------------------------------------------------------------------------
        */

        $user = $this->usersRepository->checkUser($credentials);

        if (
            $user &&
            Hash::check($credentials['password'], $user->password)
        ) {
            $token = $user
                ->createToken('mobile-app')
                ->plainTextToken;

            return [
                'token' => $token,

                'user' => $this->withContact([
                    'id' => (int) $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'user' => $user->user,

                    'company' => null,
                    'company_id' => null,
                    'employee_id' => null,

                    'language' => 'sq',
                    'account_type' => 'user',
                ], $user),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY LOGIN
        |--------------------------------------------------------------------------
        */

        $company = $this->companiesRepository->checkCompany($credentials);

        if (
            $company &&
            Hash::check($credentials['password'], $company->password)
        ) {
            if (
                strcasecmp(
                    (string) $company->status,
                    'Blocked'
                ) === 0
            ) {
                throw new \Exception(
                    __('pages.company_blocked')
                );
            }

            $token = $company
                ->createToken('mobile-app')
                ->plainTextToken;

            return [
                'token' => $token,

                'user' => $this->withContact([
                    'id' => (int) $company->id,
                    'username' => $company->username,
                    'role' => 'company',

                    'user' => $company->company,
                    'company' => $company->company,

                    'company_id' => (int) $company->id,
                    'employee_id' => null,

                    'language' => $this->normalizeLanguage(
                        $company->language ?? null
                    ),

                    'account_type' => 'company',
                ], $company),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE LOGIN
        |--------------------------------------------------------------------------
        */

        $employee = $this->employeesRepository->checkEmployee($credentials);

        if (
            $employee &&
            Hash::check($credentials['password'], $employee->password)
        ) {
            /*
             * Employee account must be active.
             */
            if (
                strcasecmp(
                    (string) $employee->status,
                    'Active'
                ) !== 0
            ) {
                throw new \Exception(
                    __('pages.employee_account_blocked')
                );
            }

            /*
             * Employee must belong to a company.
             */
            if (empty($employee->company_id)) {
                throw new \Exception(
                    __('pages.employee_no_company')
                );
            }

            /*
             * Company must not be blocked.
             */
            $companyBlocked =
                $this->companiesRepository
                    ->checkIfCompanyIsBlocked(
                        (int) $employee->company_id
                    );

            if ($companyBlocked) {
                throw new \Exception(
                    __('pages.company_blocked_staff')
                );
            }

            /*
             * Get employee's company.
             */
            $companyRow =
                $this->companiesRepository
                    ->findCompanyById(
                        (int) $employee->company_id
                    );

            if (!$companyRow) {
                throw new \Exception(
                    __('pages.employee_no_company')
                );
            }

            /*
             * Create employee Sanctum token.
             */
            $token = $employee
                ->createToken('mobile-app')
                ->plainTextToken;

            return [
                'token' => $token,

                'user' => $this->withContact([
                    'id' => (int) $employee->id,
                    'username' => $employee->username,
                    'role' => $employee->role,

                    'user' => $employee->employee,

                    'company' => $companyRow->company ?? null,

                    'company_id' => (int) $employee->company_id,
                    'employee_id' => (int) $employee->id,

                    'language' => $this->normalizeLanguage(
                        $companyRow->language ?? null
                    ),

                    'account_type' => 'employee',
                ], $employee),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | INVALID LOGIN
        |--------------------------------------------------------------------------
        */

        throw new \Exception(
            __('pages.invalid_credentials')
        );
    }

    /**
     * Return authenticated account information.
     */
    public function getAuthenticatedIdentity($account): array
    {
        if (!$account) {
            throw new \Exception('Unauthenticated.');
        }

        /*
        |--------------------------------------------------------------------------
        | MAIN USER
        |--------------------------------------------------------------------------
        */

        if ($account instanceof UsersModel) {
            return $this->withContact([
                'id' => (int) $account->id,
                'username' => $account->username,
                'role' => $account->role,

                'user' => $account->user,

                'company' => null,
                'company_id' => null,
                'employee_id' => null,

                'language' => 'sq',

                'account_type' => 'user',
            ], $account);
        }

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE
        |--------------------------------------------------------------------------
        */

        if ($this->isEmployee($account)) {
            $companyRow = null;

            if (!empty($account->company_id)) {
                $companyRow =
                    $this->companiesRepository
                        ->findCompanyById(
                            (int) $account->company_id
                        );
            }

            return $this->withContact([
                'id' => (int) $account->id,
                'username' => $account->username,
                'role' => $account->role,

                'user' => $account->employee,

                'company' => $companyRow->company ?? null,

                'company_id' => !empty($account->company_id)
                    ? (int) $account->company_id
                    : null,

                'employee_id' => (int) $account->id,

                'language' => $this->normalizeLanguage(
                    $companyRow->language ?? null
                ),

                'account_type' => 'employee',
            ], $account);
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY
        |--------------------------------------------------------------------------
        */

        if ($this->isCompany($account)) {
            return $this->withContact([
                'id' => (int) $account->id,
                'username' => $account->username,
                'role' => 'company',

                'user' => $account->company,

                'company' => $account->company,
                'company_id' => (int) $account->id,

                'employee_id' => null,

                'language' => $this->normalizeLanguage(
                    $account->language ?? null
                ),

                'account_type' => 'company',
            ], $account);
        }

        throw new \Exception(
            'Unknown account type.'
        );
    }

    /**
     * Update password of currently authenticated account.
     */
    public function updateProfilePassword(
        $account,
        string $oldPassword,
        string $newPassword
    ): bool {
        if (!$account) {
            return false;
        }

        /*
         * Check old password.
         */
        if (
            !Hash::check(
                $oldPassword,
                $account->password
            )
        ) {
            return false;
        }

        /*
         * Prevent same password.
         */
        if (
            Hash::check(
                $newPassword,
                $account->password
            )
        ) {
            return false;
        }

        /*
         * Save new hashed password.
         */
        $account->password = Hash::make(
            $newPassword
        );

        return $account->save();
    }

    // /**
    //  * Update company language.
    //  */
    // public function updateCompanyLanguage(
    //     $account,
    //     string $language
    // ): bool {
    //     if (!$account) {
    //         return false;
    //     }

    //     $language =
    //         $this->normalizeLanguage(
    //             $language
    //         );

    //     /*
    //     |--------------------------------------------------------------------------
    //     | COMPANY
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($this->isCompany($account)) {
    //         return $this
    //             ->companiesRepository->updateLanguage(
    //                 (int) $account->id,
    //                 $language
    //             );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | EMPLOYEE
    //     |--------------------------------------------------------------------------
    //     |
    //     | Employee updates the language
    //     | of the company they belong to.
    //     |
    //     | If employees should NOT be allowed
    //     | to do this, remove this block.
    //     |
    //     */

    //     if (
    //         $this->isEmployee($account) &&
    //         !empty($account->company_id)
    //     ) {
    //         return $this
    //             ->companiesRepository
    //             ->updateLanguage(
    //                 (int) $account->company_id,
    //                 $language
    //             );
    //     }

    //     return false;
    // }

    /**
     * Normalize supported languages.
     */
    private function withContact(array $identity, $account): array
    {
        $identity['email'] = !empty($account->email) ? (string) $account->email : null;
        $identity['phone'] = !empty($account->phone) ? (string) $account->phone : null;

        return $identity;
    }

    private function normalizeLanguage(
        ?string $language
    ): string {
        return in_array(
            $language,
            ['sq', 'en', 'de'],
            true
        )
            ? $language
            : 'sq';
    }

    /**
     * Determine if authenticated model
     * represents an employee.
     */
    private function isEmployee($account): bool
    {
        if (!$account) {
            return false;
        }

        return isset($account->employee)
            && isset($account->company_id);
    }

    /**
     * Determine if authenticated model
     * represents a company.
     */
    private function isCompany($account): bool
    {
        if (!$account) {
            return false;
        }

        return isset($account->company)
            && !isset($account->employee);
    }
}
