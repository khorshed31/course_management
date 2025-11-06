<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Book;

class BookPolicy
{
    public function before(User $user, $ability) {
        return $user->is_admin ?? false;
    }

    public function viewAny(User $user) { return true; }
    public function view(User $user, Book $book) { return true; }
    public function create(User $user) { return true; }
    public function update(User $user, Book $book) { return true; }
    public function delete(User $user, Book $book) { return true; }
}