<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidImportUser;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Model\User;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Psr\Http\Message\UploadedFileInterface;
use Safe\DateTimeImmutable;
use Safe\Exceptions\DatetimeException;
use function array_map;
use function array_merge;
use function assert;
use function count;
use function explode;
use function file_exists;
use function Safe\filesize;
use function Safe\sprintf;
use function str_replace;
use function strtolower;

/**
 * Use case for importing users from an xlsx file
 *
 * Column layout:
 * 1. First Name
 * 2. Last Name
 * 3. Email
 * 4. Phone
 * 5. Type
 * 6. Role(s) (comma separated list of role name)
 * 7. Company Name
 * 8. Coach Email
 * 9. Status
 * 10. Address
 * 11. LinkedIn
 * 12. Function
 * 13. Previous Function
 * 14. Seniority Date
 */
final class ImportUsers
{
    private CreateUserFromImport $importUser;
    private const ERROR_MAP = [
        'duplicate_email' => 'Il y a deux lignes ou plus avec l\'e-mail "%s"',
        'datetime_invalid' => 'La valeur de date "%s" n\'est pas valide',
    ];

    public function __construct(CreateUserFromImport $importUser)
    {
        $this->importUser = $importUser;
    }

    /**
     * @return User[]
     *
     * @throws InvalidUsersXlsx
     * @throws InvalidFileValue
     * @throws InvalidStringValue
     */
    public function import(UploadedFileInterface $file, string $rootPath): array
    {
        InvalidFileValue::isNotEmpty($file, 'file');

        $filepath = $rootPath . 'public/files/tmp_import_' . $file->getClientFilename();
        $file->moveTo($filepath);
        if (! file_exists($filepath) || filesize($filepath) === 0) {
            throw new InvalidFileValue('Upload failed', 400);
        }

        $reader = new Xlsx();
        $spreadsheet = $reader->load($filepath);
        $rows = $spreadsheet->getActiveSheet()->toArray();
        $errors = [];
        $users = [];
        $userEmailMap = [];

        // Do the first loop for error checking
        for ($i = 1; $i < count($rows); $i++) {
            try {
                // Excel can sometimes export empty rows
                if (empty($rows[$i][5])) {
                    continue;
                }

                $rows[$i] = $this->mapExcelRowToArray($rows[$i]);

                // Check for duplicates within this file
                if (isset($userEmailMap[$rows[$i]['email']])) {
                    throw new InvalidImportUser([sprintf(self::ERROR_MAP['duplicate_email'], $rows[$i]['email'])]);
                }

                $this->importUser->import($rows[$i], false);
                $users[] = $rows[$i];
                $userEmailMap[$rows[$i]['email']] = $i;
            } catch (InvalidImportUser $e) {
                $errors = array_merge($errors, array_map(static fn(string $error) => [
                    'line' => $i + 1,
                    'message' => $error,
                ], $e->getErrors()));
            } catch (DatetimeException $e) {
                $errors[] = [
                    'line' => $i + 1,
                    'message' => sprintf(self::ERROR_MAP['datetime_invalid'], $rows[$i][14]),
                ];
            }
        }

        if (! empty($errors)) {
            throw new InvalidUsersXlsx($errors);
        } else {
            foreach ($users as $k => $user) {
                $users[$k] = $this->importUser->import($user);
                assert($users[$k] instanceof User);
            }
        }

        return $users;
    }

    /**
     * @param mixed[] $row
     *
     * @return mixed[]
     */
    protected function mapExcelRowToArray(array $row): array
    {
        return [
            'civility' => str_replace('.', '', strtolower((string) $row[0])),
            'firstName' => (string) $row[1],
            'lastName' => (string) $row[2],
            'email' => (string) $row[3],
            'phone' => (string) $row[4],
            'type' => (string) $row[5],
            'roles' => ! empty($row[6]) ? explode(',', $row[6]) : [],
            'company' => $row[7],
            'coach' => $row[8],
            'status' => (string) $row[9],
            'address' => (string) $row[10],
            'linkedIn' => (string) $row[11],
            'function' => (string) $row[12],
            'previousFunction' => (string) $row[13],
            'seniorityDate' => empty($row[14]) ? null : DateTimeImmutable::createFromFormat('d/m/Y', $row[14])->format('c'),
        ];
    }
}
