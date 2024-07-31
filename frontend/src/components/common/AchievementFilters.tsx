import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFilter } from '@fortawesome/free-solid-svg-icons';
import AchievementTagButton from "./AchievementTagButton";
import { useState } from 'react';

interface AchievementFiltersProps {
    filters: { [key: string]: boolean };
    toggleFilter: (key: string) => void;
    searchQuery: string;
    setSearchQuery: (searchQuery: string) => void;
}

const AchievementFilters: React.FC<AchievementFiltersProps> = ({ filters, toggleFilter, searchQuery, setSearchQuery }) => {
    const [opened, setOpened] = useState(false);
    const [animate, setAnimate] = useState(false);

    const handleClick = () => {
        setOpened(!opened);
        setAnimate(true);
        setTimeout(() => setAnimate(false), 500);
    };

    const statusTags = [
        { tag: "Earned", key: "showEarned" },
        { tag: "Not Earned", key: "showNotEarned" },
        { tag: "Hidden", key: "showHidden" },
        { tag: "Non-Hidden", key: "showNonHidden" },
    ];

    const categoryTags = [
        { tag: "Amount of predictions", key: "showAmount" },
        { tag: "Timing and Strategy", key: "showTiming" },
        { tag: "Weekly and Cumulative", key: "showWeekly" },
        { tag: "Streaks and Trends", key: "showStreaks" },
        { tag: "Special Predictions", key: "showSpecial" },
        { tag: "Mocking Achievements", key: "showMocking" },
    ];

    return (
        <div className="flex flex-col justify-center items-center">
            <button onClick={handleClick} className="bg-gray-700 text-white p-2 rounded-lg my-2 lg:w-1/12 flex items-center">
                Show filter
                <FontAwesomeIcon
                    icon={faFilter}
                    style={{
                        color: "#bababa",
                        marginLeft: "8px",
                    }}
                    className={animate ? 'fa-beat' : ''}
                />
            </button>
            {opened && (
                <div className="lg:w-1/2 flex flex-col items-center my-3 bg-gray-600 p-3 rounded-3xl">
                    <div className="flex mb-5">
                        <h1>Search</h1>
                        <input
                            className="rounded-lg text-white bg-gray-700 mx-3 px-2"
                            type='text'
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                        />
                    </div>
                    <h2>Status:</h2>
                    <div>
                        {statusTags.map(({ tag, key }) => (
                            <AchievementTagButton
                                key={key}
                                tag={tag}
                                isClicked={filters[key]}
                                onClick={() => toggleFilter(key)}
                            />
                        ))}
                    </div>
                    <h2>Categories:</h2>
                    <div>
                        {categoryTags.map(({ tag, key }) => (
                            <AchievementTagButton
                                key={key}
                                tag={tag}
                                isClicked={filters[key]}
                                onClick={() => toggleFilter(key)}
                            />
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

export default AchievementFilters;
