<?php
namespace src\Enums;

enum ResultEnums: string {
    case CORRECT = 'Correct';
    case INCORRECT = 'Incorrect';
    case PASSED = 'Passed';
    case FAILED = 'Failed';
    case ACTIVITY_AWARD_DESCRIPTION = 'Congratulations for reaching the maximum score award.';

    case ACTIVITY_AWARD_PATH = 'path/to/activityAward-photo.jpg';

    case LESSON_AWARD_PATH = 'path/to/lessonAward-photo.jpg';

    case LESSON_AWARD_DESCRIPTION = 'Congratulations for finising the activity.';

    case IN_PROGRESS = 'In Progress';

    case WATCHED = 'Watched';
}
