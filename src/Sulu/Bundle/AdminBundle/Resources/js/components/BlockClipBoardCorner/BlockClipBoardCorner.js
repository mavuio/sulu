// @flow
import React from 'react';
import blockclipboardcornerStyles from './blockclipboardcorner.scss';

import BlockClipBoardButton from '../BlockClipBoardButton';
import {Checkbox} from 'sulu-admin-bundle/components';
import {observer} from 'mobx-react';


type Props = {|
    value: Object,
    sortIndex: number,
    multiselect: Object,
    onBlockMenuClick: (items: array<object>) => void,
|};

@observer
export default class BlockClipBoardCorner extends React.Component<Props> {

    handleCheckboxClick(position) {
        const {multiselect,onBlockMenuClick} = this.props;
        onBlockMenuClick("selection_command",{command: "toggle",position});
    }

    render() {
        const {value,sortIndex,onBlockMenuClick,multiselect} = this.props;

        return (
            <div className={blockclipboardcornerStyles.blockclipboardcorner}>
                {multiselect.enabled
                            ? <Checkbox
                                    checked={multiselect.selectedIndexes.includes(sortIndex)}
                                    className={blockclipboardcornerStyles.blockclipboardcheckbox}
                                    onChange={() => this.handleCheckboxClick(sortIndex)}
                                />
                            : <BlockClipBoardButton value={value} sortIndex={sortIndex} onBlockMenuClick={onBlockMenuClick} />
                            }
            </div>
        );
    }
}


