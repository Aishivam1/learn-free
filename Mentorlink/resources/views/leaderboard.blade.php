@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Global Leaderboard -->
        <div class="mb-5"id="text-center12">
            <h2 class="text-center mb-4"id="text-center12">Global Leaderboard</h2>
            <div class="leaderboard-container">
                @if (count($globalLeaderboard) >= 3)
                    <div class="top-3-container">
                        <!-- 2nd Place - Left Side -->
                        <div class="leader second-place">
                            <div class="rank-badge">2</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[1]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[1]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[1]->points }} pts</p>
                        </div>

                        <!-- 1st Place - Center -->
                        <div class="leader first-place">
                            <div class="rank-badge">1</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[0]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[0]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[0]->points }} pts</p>
                        </div>

                        <!-- 3rd Place - Right Side -->
                        <div class="leader third-place">
                            <div class="rank-badge">3</div>
                            <img src="{{ asset('avatar/' . ($globalLeaderboard[2]->avatar ?? 'default.png')) }}"
                                alt="Avatar">
                            <p>{{ $globalLeaderboard[2]->name }}</p>
                            <p class="points">{{ $globalLeaderboard[2]->points }} pts</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Remaining Users -->
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($globalLeaderboard as $index => $user)
                        <tr>
                            <td>{{ ($globalLeaderboard->currentPage() - 1) * $globalLeaderboard->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                    class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->points }} pts</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $globalLeaderboard->links() }}
            </div>
        </div>

        <!-- Weekly Leaderboard -->
        <div class="mb-5">
            <h2 class="text-center mb-4">Weekly Leaderboard</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeklyLeaderboard as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                    class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->points }} pts</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Category Leaderboard -->
        <!-- Category Leaderboard -->
        <div class="mb-5 category-section" id="categorySection">
            <h2 class="text-center mb-4">Category Leaderboard</h2>
            <form method="GET" action="{{ route('leaderboard') }}" id="categoryForm" class="mb-4">
                <div class="d-flex justify-content-center position-relative category-wrapper">
                    <select name="category" id="categorySelect" class="form-select w-auto custom-select">
                        <option value="courses" {{ $category == 'courses' ? 'selected' : '' }}>Courses</option>
                        <option value="quizzes" {{ $category == 'quizzes' ? 'selected' : '' }}>Quizzes</option>
                        <option value="discussions" {{ $category == 'discussions' ? 'selected' : '' }}>Discussions</option>
                    </select>
                </div>
            </form>

            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Avatar</th>
                        <th>User</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categoryLeaderboard as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ asset('avatar/' . ($user->avatar ?? 'default.png')) }}"
                                    class="leaderboard-avatar" alt="{{ $user->name }}'s avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* General Leaderboard Styling */
        .leaderboard-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .leaderboard-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 30px;
        }



        .top-3-container {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
            z-index: -1 !important;
        }

        

        .leader {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .leader img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #007bff;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }

        .leader:hover img {
            transform: scale(1.1);
        }

        .table {
            width: 100%;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        .table th {
            background: #007bff;
            color: white;
            padding: 14px;
        }

        .table td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        .small-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-right: 8px;
        }

        .nav-tabs .nav-link {
            color: #007bff;
            font-weight: bold;
        }

        .nav-tabs .nav-link.active,
        .nav-tabs .nav-link:hover {
            background: #007bff;
            color: white;
            border-radius: 5px;
        }

        .leader {
            position: relative;
        }



        .leader p {
            margin: 8px 0;
            color: #007bff;
            font-weight: bold;
        }

        .leader .points {
            font-size: 16px;
            color: #6c757d;
        }

        .first-place {
            order: 2;
            transform: scale(1.3);
            z-index: 5;
        }

        .second-place {
            order: 1;
            margin-top: 25px;
        }

        .third-place {
            order: 3;
            margin-top: 45px;
        }



        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 14px;
        }

        .table td {
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        /* Small Avatars for Table */
        .small-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #007bff;
            margin-right: 8px;
        }

        /* Category Select Specific Styles */
        .category-section {
            position: relative;
        }

        .category-wrapper {
            position: relative;
            z-index: 10000;
            /* Very high z-index to ensure it's above everything */
        }

        .custom-select {
            min-width: 200px;
            padding: 8px 12px;
            border: 2px solid #007bff;
            border-radius: 8px;
            background-color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 10001;
            /* Even higher than wrapper */

            /* Force the select to appear above other elements */
            transform: translateZ(0);
            -webkit-transform: translateZ(0);

            /* Ensure the dropdown appears */
            /*  -webkit-appearance: menulist !important;
            -moz-appearance: menulist !important;
           appearance: menulist !important; */
        }

        .custom-select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            outline: none;
        }

        /* Style for the options */
        .custom-select option {
            padding: 10px;
            background: white;
            color: #333;
        }

        /* Additional styles to ensure visibility */
        #categorySection {
            isolation: isolate;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }

        /* Override any negative z-index effects */
        .container>div {
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .top-3-container {
                flex-direction: column;
                align-items: center;
            }

            .leader img {
                width: 100px;
                height: 100px;
            }

            .first-place,
            .second-place,
            .third-place {
                margin-bottom: 10px;
                transform: scale(1);
            }
        }

        .rank-badge {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            z-index: 10;
        }


        @media (max-width: 768px) {
            .leaderboard-container {
                flex-direction: column;
                align-items: center;
            }

            .leader img {
                width: 80px;
                height: 80px;
            }

            .second-place,
            .third-place {
                margin-top: 10px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
     
        // Add this to your existing scripts
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('categorySelect');
            const categorySection = document.getElementById('categorySection');

            if (categorySelect) {
                // Store original z-index
                let originalZIndex;

                categorySelect.addEventListener('mousedown', function(e) {
                    // Temporarily elevate the z-index when opening the dropdown
                    originalZIndex = categorySection.style.zIndex;
                    categorySection.style.zIndex = '100000';
                    this.style.zIndex = '100001';
                });

                categorySelect.addEventListener('change', function() {
                    // Submit form and reset z-index
                    document.getElementById('categoryForm').submit();
                });

                categorySelect.addEventListener('blur', function() {
                    // Reset z-index after interaction
                    setTimeout(() => {
                        categorySection.style.zIndex = originalZIndex;
                        this.style.zIndex = '';
                    }, 200);
                });

                // Prevent any click events from affecting parent containers
                categorySelect.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });

        // Updated script with proper ScrollTrigger loading
        document.addEventListener('DOMContentLoaded', function() {
            // Check if GSAP is loaded
            if (typeof gsap === 'undefined') {
                // Add GSAP CDN if not already included
                const gsapScript = document.createElement('script');
                gsapScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js';
                document.head.appendChild(gsapScript);

                // Wait for GSAP to load before loading ScrollTrigger
                gsapScript.onload = function() {
                    // Now load ScrollTrigger plugin
                    const scrollTriggerScript = document.createElement('script');
                    scrollTriggerScript.src =
                        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js';
                    document.head.appendChild(scrollTriggerScript);

                    // Initialize animations after ScrollTrigger loads
                    scrollTriggerScript.onload = initAnimations;
                };
            } else {
                // If GSAP is already loaded, check ScrollTrigger
                if (typeof ScrollTrigger === 'undefined') {
                    const scrollTriggerScript = document.createElement('script');
                    scrollTriggerScript.src =
                        'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js';
                    document.head.appendChild(scrollTriggerScript);

                    scrollTriggerScript.onload = initAnimations;
                } else {
                    initAnimations();
                }
            }

            function initAnimations() {
                // Register ScrollTrigger plugin
                gsap.registerPlugin(ScrollTrigger);

                // Animate page title with a fade in
                gsap.from('h2', {
                    opacity: 0,
                    y: -50,
                    stagger: 0.3,
                    duration: 1,
                    ease: 'power3.out',
                    scrollTrigger: {
                        trigger: 'h2',
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                });

                // Animate the top 3 leaders
                if (document.querySelector('.top-3-container')) {
                    // Staggered entrance for top 3
                    gsap.from('.leader', {
                        opacity: 0,
                        y: 100,
                        scale: 0.5,
                        stagger: 0.2,
                        duration: 1.2,
                        ease: 'back.out(1.7)',
                        scrollTrigger: {
                            trigger: '.top-3-container',
                            start: 'top 80%',
                            toggleActions: 'play none none none'
                        }
                    });

                    // Bouncing animation for rank badges
                    gsap.from('.rank-badge', {
                        scale: 0,
                        rotation: 360,
                        duration: 0.8,
                        stagger: 0.2,
                        ease: 'elastic.out(1, 0.3)',
                        scrollTrigger: {
                            trigger: '.rank-badge',
                            start: 'top 80%',
                            toggleActions: 'play none none none'
                        }
                    });

                    // Subtle pulse animation for the first place
                    gsap.to('.first-place img', {
                        scale: 1.05,
                        yoyo: true,
                        repeat: -1,
                        duration: 1.5,
                        ease: 'sine.inOut'
                    });

                    // Hover animations for leader images
                    document.querySelectorAll('.leader').forEach(leader => {
                        leader.addEventListener('mouseenter', function() {
                            gsap.to(this.querySelector('img'), {
                                boxShadow: '0 0 20px rgba(0, 123, 255, 0.8)',
                                duration: 0.3
                            });
                        });

                        leader.addEventListener('mouseleave', function() {
                            gsap.to(this.querySelector('img'), {
                                boxShadow: '0 0 0px rgba(0, 123, 255, 0)',
                                duration: 0.3
                            });
                        });
                    });
                }

                // Animate table rows with staggered entrance
                const tables = document.querySelectorAll('.table');
                tables.forEach((table, index) => {
                    const rows = table.querySelectorAll('tbody tr');
                    gsap.from(rows, {
                        opacity: 0,
                        x: -50,
                        stagger: 0.1,
                        duration: 0.5,
                        ease: 'power1.out',
                        scrollTrigger: {
                            trigger: table,
                            start: 'top 80%',
                            toggleActions: 'play none none none'
                        }
                    });
                });

                // Add hover animation for table rows
                document.querySelectorAll('tbody tr').forEach(row => {
                    row.addEventListener('mouseenter', function() {
                        gsap.to(this, {
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            duration: 0.3
                        });
                    });

                    row.addEventListener('mouseleave', function() {
                        gsap.to(this, {
                            backgroundColor: 'transparent',
                            duration: 0.3
                        });
                    });
                });

                // Animate category selector
                if (document.getElementById('categorySelect')) {
                    gsap.from('#categorySelect', {
                        opacity: 0,
                        y: 20,
                        duration: 0.8,
                        scrollTrigger: {
                            trigger: '#categorySelect',
                            start: 'top 90%',
                            toggleActions: 'play none none none'
                        }
                    });
                }

                // Add a subtle animation to the leaderboard avatars
                gsap.from('.leaderboard-avatar', {
                    opacity: 0,
                    scale: 0,
                    stagger: 0.05,
                    duration: 0.5,
                    ease: 'back.out',
                    scrollTrigger: {
                        trigger: '.leaderboard-avatar',
                        start: 'top 85%',
                        toggleActions: 'play none none none'
                    }
                });

                console.log('GSAP animations initialized with ScrollTrigger');
            }
        });
    </script>
@endpush
