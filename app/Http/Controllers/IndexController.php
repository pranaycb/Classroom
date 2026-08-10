<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Classroom;

class IndexController extends Controller
{
    /**
     * Render a view page.
     */
    public function __invoke()
    {
        $data = [
            'hero' => [
                'title' => 'Where teaching and learning come together',
                'subtitle' => 'Transform your classroom experience with tools designed for modern educators and students.',
                'counter' => [
                    'classrooms' => Classroom::count(),
                    'students' => User::count(),
                ],
            ],
            'features' => [
                [
                    'icon' => 'fas fa-lock',
                    'title' => 'Private Classroom',
                    'subtitle' => 'The private classroom feature ensures a secure and organized learning environment. Teachers and moderators can manage students, monitor activities, and restrict interactions during sessions.',
                ],
                [
                    'icon' => 'fas fa-chalkboard-user',
                    'title' => 'Live Classes',
                    'subtitle' => 'The Live Class feature allows teachers to conduct realtime virtual lessons, creating an interactive classroom experience. Students can join from anywhere, participate actively, and engage with the teacher through audio, video, and chat functionalities. ',
                ],
                [
                    'icon' => 'fas fa-users',
                    'title' => 'Online Exam System',
                    'subtitle' => 'The Online Exam System allows teachers to create and conduct MCQ-based exams digitally, enabling students to take tests from anywhere. Teachers can set schedules, duration, and exam rules, providing a flexible and efficient assessment process.',
                ],
                [
                    'icon' => 'fas fa-robot',
                    'title' => 'AI Learning Assistant',
                    'subtitle' => 'The AI Learning Assistant is designed to help students by answering their questions in real-time. Students can ask about lessons, concepts, or assignments, and the AI provides instant, relevant responses',
                ],
                [
                    'icon' => 'fas fa-bullhorn',
                    'title' => 'Announce Important Updates',
                    'subtitle' => 'The Announcements feature allows teachers to share important updates, reminders, or instructions with all students in a classroom. Messages are sent instantly, ensuring that everyone stays informed.',
                ],
                [
                    'icon' => 'fas fa-book-open-reader',
                    'title' => 'Evaluation Throuh Assignment',
                    'subtitle' => 'Teachers can assess student performance by reviewing submitted assignments. Each submission is evaluated based on accuracy, completeness, and adherence to guidelines. The system tracks submission deadlines and ensures students submit work on time.',
                ]
            ],
            'cta' => [
                'title' => 'Start Teaching and Learning Smarter Today',
                'subtitle' => 'Join hindreds of educators and students using our platform to manage courses, track progress, and collaborate effortlessly. Sign up and transform your classroom experience in minutes.',
            ],
        ];

        return Inertia::render('Index', $data);
    }
}
