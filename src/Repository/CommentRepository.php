<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * The function `findCommentPaginated` retrieves a paginated list of comments based on the provided
     * page number, figure ID, and optional limit.
     * 
     * @param int page The page parameter is used to specify the current page number for pagination. It
     * determines which set of comments should be returned.
     * @param int id The "id" parameter is the identifier of the figure for which you want to find the
     * comments. It is used in the query to filter the comments based on the figure.
     * @param int limit The limit parameter determines the maximum number of comments to retrieve per
     * page. By default, it is set to 10, but you can change it to any positive integer value.
     * 
     * @return array an array with the following keys:
     * - 'data': an array of Comment objects that match the given criteria
     * - 'pages': the total number of pages based on the given limit
     * - 'page': the current page number
     * - 'limit': the number of comments to be displayed per page
     */
    public function findCommentPaginated(int $page, int $id, int $limit = 10): array
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('c')
        ->from('App\Entity\Comment', 'c')
        ->where("c.figure = '$id'")
        ->setMaxResults($limit)
        ->setFirstResult($page * $limit - $limit);

        $paginator = new Paginator($query);
        $data = $paginator->getQuery()->getResult();

        if(empty($data)) {
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
        
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
