interface AchievementCardProps {
    achievement: {
        id: number;
        name: string;
        image: string;
        flavorText: string;
        description: string;
        category: string;
        dateEarned: string;
        earnedPercentage: number;
    };
    isMobile: boolean;
}



const AchievementCard: React.FC<AchievementCardProps> = ({ achievement, isMobile }) => {

    // On mobile, the card shall display the date earned and the percentage of players who have earned the achievement
    // on desktop not, because this information is already available in the achievementRow
    return (
        <div key={achievement.id} className="bg-gray-700 text-white shadow-md rounded-lg p-4">
            <h1 className="text-xl font-bold mb-2">{achievement.name}</h1>
            <img className="w-full h-2/3 object-cover mb-2 rounded-lg" src={`/assets/images/achievements/${achievement.image}`} alt={achievement.name} />
            {achievement.hidden ? <p className="my-3 text-yellow-300"><i>hidden achievement</i></p> : null}
            <p className="mb-2">{achievement.description}</p>
            <p className="mb-3 italic font-bold">{achievement.flavorText}</p>
            {isMobile ? achievement.dateEarned === null ? <p>Not Earned</p> : <p> Earned: {achievement.dateEarned}</p> : null}
            {isMobile ? achievement.earnedPercentage ? <p>{achievement.earnedPercentage.toFixed(2)}% of players have this achievement</p> : <p>No player has this achievement yet</p> : null}
            <p className="text-xs mt-2">{achievement.category}</p>
        </div>
    );
}

export default AchievementCard;