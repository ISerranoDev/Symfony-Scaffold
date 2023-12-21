<?php

namespace App\Repository\User;

use App\Entity\Counseling\Counseling;
use App\Entity\GeneralManagement\GeneralManagement;
use App\Entity\User\User;
use App\Utils\Classes\FilterService;
use App\Utils\Interfaces\FilteredRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, FilteredRepositoryInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private PaginatorInterface $paginator;

    public function __construct(
        ManagerRegistry $registry,
        UserPasswordHasherInterface $passwordHasher,
        PaginatorInterface $paginator
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->paginator = $paginator;
        parent::__construct($registry, User::class);
    }

    public function list(FilterService $filterService): PaginationInterface
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.roles', 'uhr')
            ->leftJoin('uhr.role', 'r')
            ->addSelect('uhr')
            ->addSelect('r')
        ;

        $this->addFilters($filterService, $query);
        $this->addOrders($filterService, $query);

        return $this->paginator->paginate(
            $query,
            $filterService->page,
            $filterService->limit
        );

    }

    public function addFilters(FilterService $filter, QueryBuilder $query): void
    {
        foreach ($filter->getFilters() as $key => $value){
            switch ($key){
                case 'username':
                    $query->andWhere('u.username LIKE :username')
                        ->setParameter('username', "%".$value."%")
                    ;
                    break;

                case 'email':
                    $query->andWhere('u.email LIKE :email')
                        ->setParameter('email', "%".$value."%")
                    ;
                    break;

                case 'enabled' && $value !== '':
                    $query->andWhere('u.enabled = :enabled')
                        ->setParameter('enabled', $value)
                    ;
                    break;

                case 'roles' && $value !== '':
                    $query->andWhere('r.id = :role')
                        ->setParameter('role', $value)
                    ;
                    break;

            }
        }
    }

    public function addOrders(FilterService $filter, QueryBuilder $query): void
    {
        foreach ($filter->getOrders() as $key => $value){

            switch ($key){
                case 'username':
                    $query->addOrderBy('u.username', $value);
                    break;

                case 'email':
                    $query->addOrderBy('u.email', $value);
                    break;

                case 'enabled':
                    $query->addOrderBy('u.enabled', $value);
                    break;

                case 'roles':
                    $query->addOrderBy('r.label', $value);
                    break;

            }
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        $newPassword = $this->passwordHasher->hashPassword($user, $newHashedPassword);
        $user->setPassword($newPassword);
    }

    public function createUser(
        string $username,
        string $password,
        string $email,
        array $roles,
        ?string $name = null,
        ?string $surname1 = null,
        ?string $surname2 = null,
        bool $enabled = true,
        ?\DateTime $entryDate = new \DateTime(),
        ?\DateTime $leavingDate = null
    ): User
    {
        $user = (new User())
            ->setUsername($username)
            ->setPassword($password)
            ->setEmail($email)
            ->setEnabled($enabled)
            ->setEntryDate($entryDate)
            ->setLeavingDate($leavingDate)
            ->setRoles($roles)
            ->setName($name)
            ->setSurname1($surname1)
            ->setSurname2($surname2)
        ;

        $this->upgradePassword($user, $password);

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function editUser(
        User $user,
        string $username,
        ?string $password,
        string $email,
        array $roles,
        ?string $name = null,
        ?string $surname1 = null,
        ?string $surname2 = null,
        bool $enabled = true,
        ?\DateTime $entryDate = null,
        ?\DateTime $leavingDate = null
    ): User
    {
        $user
            ->setUsername($username)
            ->setEmail($email)
            ->setEnabled($enabled)
            ->setEntryDate($entryDate)
            ->setLeavingDate($leavingDate)
            ->setRoles($roles)
            ->setName($name)
            ->setSurname1($surname1)
            ->setSurname2($surname2)
        ;

        if($password){
            $user->setPassword($password);
            $this->upgradePassword($user, $password);
        }

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function editProfile(
        User $user,
        ?string $password,
        ?string $name = null,
        ?string $surname1 = null,
        ?string $surname2 = null,
        ?\DateTime $entryDate = null,
        ?\DateTime $leavingDate = null
    ): User
    {
        $user
            ->setEntryDate($entryDate)
            ->setLeavingDate($leavingDate)
            ->setName($name)
            ->setSurname1($surname1)
            ->setSurname2($surname2)
        ;

        if($password){
            $user->setPassword($password);
            $this->upgradePassword($user, $password);
        }

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function updateRecovery(
        User $user,
        ?string $code,
        ?\DateTime $expirationDate
    ): void
    {
        $user->setRecoverCode($code)
            ->setRecoverCodeExpiration($expirationDate);

        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function deleteUser(
        User $user
    ): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }


}
