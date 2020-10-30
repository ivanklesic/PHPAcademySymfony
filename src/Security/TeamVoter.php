<?php
namespace App\Security;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TeamVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof Team) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Team $team */
        $team = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($team, $user);
            case self::DELETE:
                return $this->canDelete($team, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Team $team, User $user)
    {
        if($team->getLeader() === $user){
            return true;
        }

        return false;
    }

    private function canDelete(Team $team, User $user)
    {
        if($team->getLeader() === $user || $this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        return false;
    }


}