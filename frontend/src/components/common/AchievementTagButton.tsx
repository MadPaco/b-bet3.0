import { useState } from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheck } from '@fortawesome/free-solid-svg-icons';

interface AchievementTagButtonProps {
    tag: string;
    isClicked: boolean;
    onClick: () => void;
}

const AchievementTagButton: React.FC<AchievementTagButtonProps> = ({ tag, isClicked, onClick }) => {
    const [animate, setAnimate] = useState(false);

    const handleAnimation = () => {
        setAnimate(true);
        setTimeout(() => setAnimate(false), 500); // Assuming the bounce animation duration is 0.5 seconds
    };

    const combinedClickHandler = () => {
        onClick();
        handleAnimation();
    };

    return (
        <button
            onClick={combinedClickHandler}
            className={`py-2 m-2 px-4 text-highlightCream rounded-lg border-highlightCream ${isClicked ? 'bg-gray-800 border-2' : 'bg-gray-700 border-2 border-dashed border-white text-white'}`}
        >
            {tag}
            {isClicked && (<FontAwesomeIcon
                style={{
                    color: "#bababa",
                    marginLeft: "8px",
                    '--fa-animation-duration': '0.5s',
                    '--fa-animation-iteration-count': '1'
                }}
                className={animate ? 'fa-bounce' : ''}
                icon={faCheck}
            />)
            }

        </button>
    );
};

export default AchievementTagButton;
