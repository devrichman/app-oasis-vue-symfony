<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\ImportUsers;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Model\Company;
use App\Domain\Repository\CompanyRepository;
use App\Tests\Application\ApplicationTestCase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Zend\Diactoros\UploadedFile;
use function count;
use function explode;
use function file_exists;
use function filesize;
use function getenv;
use function mkdir;
use function rmdir;
use function scandir;
use function unlink;
use const UPLOAD_ERR_OK;

class ImportUsersTest extends ApplicationTestCase
{
    protected ImportUsers $importUsers;
    protected string $basedir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importUsers = self::$container->get(ImportUsers::class);

        $this->basedir = getenv('ROOT_PATH') . 'public/files/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir);
        }

        $company = new Company('Test Company Name', 'TEST_COMPANY_1');
        self::$container->get(CompanyRepository::class)->save($company);
    }

    protected function tearDown(): void
    {
        foreach (scandir($this->basedir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            unlink($this->basedir . '/' . $file);
        }
        rmdir($this->basedir);

        parent::tearDown();
    }

    public function testImportUsers(): void
    {
        $data = [
            ['Civility', 'First Name', 'Last Name', 'Email', 'Phone', 'Type', 'Role(s)', 'Company Name', 'Coach Email', 'Actif', 'Address', 'LinkedIn', 'Function', 'Previous Function', 'Seniority Date'],
            ['M', 'Test', 'User', 'test@user.com', '123123123', 'Candidat', 'Administrateur', 'Test Company Name', $this->loggedUser->getEmail(), 'oui', '123 Paris', 'https://linkedin.com/in/test', 'Test Function', 'Test Prev Function', '28/01/2019'],
            ['M', 'Second', 'User', 'second@user.com', '234234234', 'Candidat', 'Administrateur', 'Test Company Name', $this->loggedUser->getEmail(), 'oui', '123 Paris new street', 'https://linkedin.com/in/test2', 'Test Function 2', 'Test Prev Function 2', '01/01/2019'],
        ];

        $file = $this->generateTestFile($data);

        $users = $this->importUsers->import($file, getenv('ROOT_PATH'));
        $this->assertCount(2, $users);

        foreach ($users as $i => $user) {
            $row = $data[$i + 1];
            $this->assertEquals($row[1], $user->getFirstName());
            $this->assertEquals($row[2], $user->getLastName());
            $this->assertEquals($row[3], $user->getEmail());
            $this->assertEquals($row[4], $user->getPhone());
            $this->assertEquals('candidate', $user->getType()->getId());
            $this->assertEquals($row[6], $user->getRolesByUsersRoles()[0]->getName());
            $this->assertEquals($row[7], $user->getCompany()->getName());
            $this->assertEquals($row[8], $user->getCoach()->getEmail());
            $this->assertEquals($row[10], $user->getAddress());
            $this->assertEquals($row[11], $user->getLinkedin());
            $this->assertEquals($row[12], $user->getFunction());
            $this->assertEquals($row[13], $user->getPreviousFunction());
        }
    }

    public function testImportErrorDuplicateUser(): void
    {
        $data = [
            ['Civility', 'First Name', 'Last Name', 'Email', 'Phone', 'Type', 'Role(s)', 'Company Name', 'Coach Email', 'Actif', 'Address', 'LinkedIn', 'Function', 'Previous Function', 'Seniority Date'],
            ['M', 'Test', 'User', $this->loggedUser->getEmail(), '123123123', 'Candidat', 'Administrateur', 'Test Company Name', $this->loggedUser->getEmail(), 'oui', '123 Paris', 'https://linkedin.com/in/test', 'Test Function', 'Test Prev Function', '28/01/2019'],
        ];

        $file = $this->generateTestFile($data);

        $this->expectException(InvalidUsersXlsx::class);
        $this->importUsers->import($file, getenv('ROOT_PATH'));
    }

    public function testImportErrorInvalidCompany(): void
    {
        $data = [
            ['Civility', 'First Name', 'Last Name', 'Email', 'Phone', 'Type', 'Role(s)', 'Company Name', 'Coach Email', 'Actif', 'Address', 'LinkedIn', 'Function', 'Previous Function', 'Seniority Date'],
            ['M', 'Test', 'User', 'first@testuser.com', '123123123', 'Candidat', 'Administrateur', 'Test Company', $this->loggedUser->getEmail(), 'oui', '123 Paris', 'https://linkedin.com/in/test', 'Test Function', 'Test Prev Function', '28/01/2019'],
        ];

        $file = $this->generateTestFile($data);

        $this->expectException(InvalidUsersXlsx::class);
        $this->importUsers->import($file, getenv('ROOT_PATH'));
    }

    public function testImportErrorInvalidRole(): void
    {
        $data = [
            ['Civility', 'First Name', 'Last Name', 'Email', 'Phone', 'Type', 'Role(s)', 'Company Name', 'Coach Email', 'Actif', 'Address', 'LinkedIn', 'Function', 'Previous Function', 'Seniority Date'],
            ['M', 'Test', 'User', 'first@testuser.com', '123123123', 'Candidat', 'Invalid Role', 'Test Company Name', $this->loggedUser->getEmail(), 'oui', '123 Paris', 'https://linkedin.com/in/test', 'Test Function', 'Test Prev Function', '28/01/2019'],
        ];

        $file = $this->generateTestFile($data);

        $this->expectException(InvalidUsersXlsx::class);
        $this->importUsers->import($file, getenv('ROOT_PATH'));
    }

    public function testImportMultipleErrors(): void
    {
        $data = [
            ['Civility', 'First Name', 'Last Name', 'Email', 'Phone', 'Type', 'Role(s)', 'Company Name', 'Coach Email', 'Actif', 'Address', 'LinkedIn', 'Function', 'Previous Function', 'Seniority Date'],
            ['M', '', 'User', 'first@testuser.com', '11211', 'test', 'Invalid Role', 'Test Company Name', $this->loggedUser->getEmail(), 'oui', '123 Paris', 'https://linkedin.com/in/test', 'Test Function', 'Test Prev Function', '28/01/2019'],
        ];

        $file = $this->generateTestFile($data);

        try {
            $this->importUsers->import($file, getenv('ROOT_PATH'));
            $this->fail('Exception needs to be thrown');
        } catch (InvalidUsersXlsx $e) {
            $this->assertEquals(5, count(explode('<br />', $e->getMessage())));
        }
    }

    /**
     * @param mixed[] $data
     */
    protected function generateTestFile(array $data): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($this->basedir . '/test.xlsx');

        return new UploadedFile($this->basedir . '/test.xlsx', filesize($this->basedir . '/test.xlsx'), UPLOAD_ERR_OK, 'test.xlsx');
    }
}
