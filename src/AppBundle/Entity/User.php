<?php
// src/Acme/UserBundle/Entity/User.php

namespace AppBundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


use JMS\Serializer\Annotation as JMS;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"follows"})
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /** @ORM\PreUpdate */
    public function setCounts()
    {
        $this->followersCount = $this->followers->count();
    }

    /**
     * Set followersCount
     *
     * @param integer $followersCount
     * @return BookList
     */
    public function setFollowersCount($followersCount)
    {
        $this->followersCount = $followersCount;

        return $this;
    }

    /**
     * Get followersCount
     *
     * @return integer
     */
    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPublicProfile(){
        return $this->publicProfile;
    }

    public function setPublicProfile($publicProfile){
        $this->publicProfile = $publicProfile;
        return $this;
    }

    public function getSlug(){
        return $this->slug;
    }

    public function setSlug($slug){
        $this->slug = $slug;
        return $this;
    }

    public function getGlobalFollow(){
        return $this->globalFollow;
    }

    public function setGlobalFollow($globalFollow){
        $this->globalFollow = $globalFollow;
        return $this;
    }

    /**
     * Set oldId
     *
     * @param integer $oldId
     * @return User
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * Set created

     *
     * @param \DateTime $created
     * @return Book
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Book
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function getCita(){
        return $this->cita;
    }

    public function setCita($cita){
        $this->cita = $cita;
        return $this;
    }

    /**
     * Set imageDir
     *
     * @param string $imageDir
     * @return Book
     */
    public function setImageDir($imageDir)
    {
        $this->imageDir = $imageDir;

        return $this;
    }

    /**
     * Get imageDir
     *
     * @return string
     */
    public function getImageDir()
    {
        return $this->imageDir;
    }

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        $this->updated= new \DateTime();
    }

    public function getFile(){
        return $this->file;
    }


    /** @ORM\PrePersist */
     public function prePersist() {
        $this->upload();
    }

     /** @ORM\PreUpdate */
    public function preUpdate() {
        $this->upload();
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {

        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $path=$this->generateRoute();
        $filename = sha1(uniqid(mt_rand(), true));
        $filename = $filename.'.'.$this->getFile()->guessExtension();

        $fs=new Filesystem();
        if(!$fs->exists($path))
        {
                 $fs->mkdir($path,0775);
        }
        $this->getFile()->move($path,$filename);

        $this->imageDir = $this->getUploadDir().$filename;

    }

    public function getAbsolutePath()
    {
        return null === $this->imageDir
            ? null
            : $this->getUploadRootDir().'/'.$this->imageDir;
    }

    public function getWebPath()
    {
        return null === $this->imageDir
            ? null
            : $this->getUploadDir().'/'.$this->imageDir;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/wp-content/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return '/uploads/avatars/'.date('mY')."/";
    }

    protected function generateRoute()
    {
        return $this->getUploadRootDir();
    }

    /**
     * Add favourites
     *
     * @param \AppBundle\Entity\Book $favourites
     * @return User
     */
    public function addFavourite(\AppBundle\Entity\Book $favourites)
    {
        $this->favourites[] = $favourites;

        return $this;
    }

    /**
     * Remove favourites
     *
     * @param \AppBundle\Entity\Book $favourites
     */
    public function removeFavourite(\AppBundle\Entity\Book $favourites)
    {
        $this->favourites->removeElement($favourites);
    }

    /**
     * Get favourites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavourites()
    {
        return $this->favourites;
    }

    /**
     * Add lists
     *
     * @param \AppBundle\Entity\BookList $lists
     * @return User
     */
    public function addList(\AppBundle\Entity\BookList $lists)
    {
        $this->lists[] = $lists;

        return $this;
    }

    /**
     * Remove lists
     *
     * @param \AppBundle\Entity\BookList $lists
     */
    public function removeList(\AppBundle\Entity\BookList $lists)
    {
        $this->lists->removeElement($lists);
    }

    /**
     * Get lists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLists()
    {
        return $this->lists;
    }

    public function getStatuses(){
        return $this->statuses;
    }

    public function addStatus(\AppBundle\Entity\UserStatus $status)
    {
        $this->statuses[] = $status;
        return $this;
    }

    public function removeStatus(\AppBundle\Entity\UserStatus $status)
    {
        $this->statuses->removeElement($status);
    }

    /**
     * Add bookRatings
     *
     * @param \AppBundle\Entity\Rating $bookRatings
     * @return User
     */
    public function addBookRating(\AppBundle\Entity\BookRating $bookRatings)
    {
        $this->bookRatings[] = $bookRatings;

        return $this;
    }

    /**
     * Remove bookRatings
     *
     * @param \AppBundle\Entity\Rating $bookRatings
     */
    public function removeBookRating(\AppBundle\Entity\BookRating $bookRatings)
    {
        $this->bookRatings->removeElement($bookRatings);
    }

    /**
     * Get bookRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookRatings()
    {
        return $this->bookRatings;
    }

    /**
     * Add reviewRatings
     *
     * @param \AppBundle\Entity\ReviewRating $reviewRatings
     * @return User
     */
    public function addReviewRating(\AppBundle\Entity\ReviewRating $reviewRatings)
    {
        $this->reviewRatings[] = $reviewRatings;

        return $this;
    }

    /**
     * Remove reviewRatings
     *
     * @param \AppBundle\Entity\ReviewRating $reviewRatings
     */
    public function removeReviewRating(\AppBundle\Entity\ReviewRating $reviewRatings)
    {
        $this->reviewRatings->removeElement($reviewRatings);
    }

    /**
     * Get reviewRatings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviewRatings()
    {
        return $this->reviewRatings;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     * @return User
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get review
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Add listsFollowed
     *
     * @param \AppBundle\Entity\BookList $listsFollowed
     * @return User
     */
    public function addListsFollowed(\AppBundle\Entity\BookList $listsFollowed)
    {
        $this->listsFollowed[] = $listsFollowed;

        return $this;
    }

    /**
     * Remove listsFollowed
     *
     * @param \AppBundle\Entity\BookList $listsFollowed
     */
    public function removeListsFollowed(\AppBundle\Entity\BookList $listsFollowed)
    {
        $this->listsFollowed->removeElement($listsFollowed);
    }

    /**
     * Get listsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListsFollowed()
    {
        return $this->listsFollowed;
    }

    /**
     * Add bookRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $bookRelations
     * @return User
     */
    public function addBookRelation(\AppBundle\Entity\BookUserRelation $bookRelations)
    {
        $this->bookRelations[] = $bookRelations;

        return $this;
    }

    /**
     * Remove bookRelations
     *
     * @param \AppBundle\Entity\BookUserRelation $bookRelations
     */
    public function removeBookRelation(\AppBundle\Entity\BookUserRelation $bookRelations)
    {
        $this->bookRelations->removeElement($bookRelations);
    }

    /**
     * Get bookRelations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookRelations()
    {
        return $this->bookRelations;
    }

    /**
     * Add booksFollowed
     *
     * @param \AppBundle\Entity\Book $booksFollowed
     * @return User
     */
    public function addBooksFollowed(\AppBundle\Entity\Book $booksFollowed)
    {
        $this->booksFollowed[] = $booksFollowed;

        return $this;
    }

    /**
     * Remove booksFollowed
     *
     * @param \AppBundle\Entity\Book $booksFollowed
     */
    public function removeBooksFollowed(\AppBundle\Entity\Book $booksFollowed)
    {
        $this->booksFollowed->removeElement($booksFollowed);
    }

    /**
     * Get booksFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooksFollowed()
    {
        return $this->booksFollowed;
    }

    /**
     * Add authorsFollowed
     *
     * @param \AppBundle\Entity\Author $authorsFollowed
     * @return User
     */
    public function addAuthorsFollowed(\AppBundle\Entity\Author $authorsFollowed)
    {
        $this->authorsFollowed[] = $authorsFollowed;

        return $this;
    }

    /**
     * Remove authorsFollowed
     *
     * @param \AppBundle\Entity\Author $authorsFollowed
     */
    public function removeAuthorsFollowed(\AppBundle\Entity\Author $authorsFollowed)
    {
        $this->authorsFollowed->removeElement($authorsFollowed);
    }

    /**
     * Get authorsFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorsFollowed()
    {
        return $this->authorsFollowed;
    }

    /**
     * Add usersFollowed
     *
     * @param \AppBundle\Entity\User $usersFollowed
     * @return User
     */
    public function addUsersFollowed(\AppBundle\Entity\User $usersFollowed)
    {
        $this->usersFollowed[] = $usersFollowed;

        return $this;
    }

    /**
     * Remove usersFollowed
     *
     * @param \AppBundle\Entity\User $usersFollowed
     */
    public function removeUsersFollowed(\AppBundle\Entity\User $usersFollowed)
    {
        $this->usersFollowed->removeElement($usersFollowed);
    }

    /**
     * Get usersFollowed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsersFollowed()
    {
        return $this->usersFollowed;
    }

    /**
     * Add followers
     *
     * @param \AppBundle\Entity\User $followers
     * @return User
     */
    public function addFollower(\AppBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \AppBundle\Entity\User $followers
     */
    public function removeFollower(\AppBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add comments
     *
     * @param \AppBundle\Entity\Comment $comments
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \AppBundle\Entity\Comment $comments
     */
    public function removeComment(\AppBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }
}
