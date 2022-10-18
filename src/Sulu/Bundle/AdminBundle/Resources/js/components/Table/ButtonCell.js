// @flow
import React from 'react';
import Icon from '../Icon';
import Cell from './Cell';
import tableStyles from './table.scss';

type Props = {|
    disabled: boolean,
    icon: string,
    onClick: ?(rowId: string | number, rowIndex: number) => void,
    rowId: string | number,
    rowIndex: number,
|};

export default class ButtonCell extends React.PureComponent<Props> {
    static defaultProps = {
        disabled: false,
    };


    // mavu added real a-tags here: 2022-10-18

    handleClick = (event: SyntheticEvent<HTMLButtonElement>) => {
        event.preventDefault(); // do not follow href by default

        const {rowIndex, onClick, rowId} = this.props;

        if (onClick) {
            onClick(rowId, rowIndex);
        }
    };

    render() {
        const {
            disabled,
            icon,
            rowId,
        } = this.props;

        if(typeof rowId === 'string'  && icon=='su-pen' && !disabled){
        return (
            <Cell className={tableStyles.buttonCell}>
                <a  href={`/admin/#/webspaces/mainsite/pages/de/${rowId}/details`}
                    onClick={this.handleClick}>
                    <Icon name={icon} />
                </a>
            </Cell>
        );
        } else {
        return (
            <Cell className={tableStyles.buttonCell}>
                <button disabled={disabled} onClick={this.handleClick} type="button">
                    <Icon name={icon} />
                </button>
            </Cell>
        );
        }
    }
}
