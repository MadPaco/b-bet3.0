import { useState } from "react";
import AchievementCard from "./AchievementCard";
import { faCircleXmark } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

interface AchievementRowProps {
    achievement: {
        id: number;
        name: string;
        image: string;
        flavorText: string;
        description: string;
        category: string;
        dateEarned: string;
        earnedPercentage: number;
        hidden: boolean;
    };
}

const AchievementRow: React.FC<AchievementRowProps> = ({ achievement }) => {
    const [showPopover, setShowPopover] = useState(false);

    const handleClick = () => {
        setShowPopover(true);
    };

    const handleClosePopover = () => {
        setShowPopover(false);
    };

    return (
        <>
            <div key={achievement.id} onClick={handleClick} className={`bg-gray-700 text-white shadow-md rounded-lg px-3 py-5 flex m-2 cursor-pointer md:w-2/3 ${achievement.hidden ? 'border border-yellow-300' : ''}`}>
                <div className="flex flex-col md:w-1/12 align-middle justify-center">
                    <img
                        className={`w-full h-full object-cover rounded-lg ${achievement.dateEarned ? '' : 'filter grayscale'}`}
                        src={`/assets/images/achievements/${achievement.image}`}
                        alt={achievement.name}
                    />
                </div>
                <div className="flex w-8/12 flex-col text-left align-middle justify-center ml-3">
                    <h1 className="text-xl font-bold">{achievement.name}</h1>
                    <p className="mb-2">{achievement.description}</p>
                </div>
                <div className="w-3/12 flex text-center align-middle justify-center items-center flex-col">
                    {achievement.dateEarned === null ? <p>Not Earned</p> : <p> Earned: {achievement.dateEarned}</p>}
                    {achievement.earnedPercentage ? <p>{achievement.earnedPercentage.toFixed(2)}% of players have this achievement</p> : <p>No player has this achievement yet</p>}
                </div>
            </div >
            {showPopover && (
                <div className="h-full w-full fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
                    <div className="relative rounded-lg shadow-lg p-4 w-3/4 md:w-1/3 bg-gray-500">
                        <button onClick={handleClosePopover} className="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 z-20 text-white hover:text-red-600 text-3xl">
                            <FontAwesomeIcon icon={faCircleXmark} />
                        </button>
                        <AchievementCard achievement={achievement} isMobile={false} />
                    </div>
                </div>
            )
            }
        </>
    );
};

export default AchievementRow;
