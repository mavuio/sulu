// @flow
import React, {Fragment} from 'react';
import classNames from 'classnames';
import Icon from '../Icon';
import Popover from '../Popover';
import SingleSelect from '../SingleSelect';
import blockStyles from './block.scss';
import type {Node} from 'react';
import {action, computed, observable} from 'mobx';
import {observer} from 'mobx-react';



type Props<T: string> = {
    activeType?: T,
    children: Node,
    dragHandle?: Node,
    expanded: boolean,
    icons?: Array<string>,
    onCollapse?: () => void,
    onExpand?: () => void,
    onRemove?: () => void,
    onSettingsClick?: () => void,
    onTypeChange?: (type: T) => void,
    types?: {[key: T]: string},
};

@observer
export default class Block<T: string> extends React.Component<Props<T>> {
    static defaultProps: {
        expanded: false,
    };

    dropdownButtonRef: ?ElementRef<'button'>;
    @observable dropdownOpen = false;



    handleCollapse = () => {
        const {expanded, onCollapse} = this.props;
        if (expanded && onCollapse) {
            onCollapse();
        }
    };

    handleExpand = () => {
        console.log('#log 3107 handle expand');
        const {expanded, onExpand} = this.props;
        if (!expanded && onExpand) {
            onExpand();
        }
    };

    handleTypeChange: (type: T) => void = (type) => {
        const {onTypeChange} = this.props;

        if (onTypeChange) {
            onTypeChange(type);
        }
    };

    setDropdownButtonRef = (ref: ?ElementRef<'button'>) => {
        this.dropdownButtonRef = ref;
    };


    @action handleDropdownToggle = (event) => {
        event.stopPropagation();
        console.log('#log 1021 open', event);
        this.dropdownOpen = !this.dropdownOpen;
    };

    @action handleDropdownClose = (event) => {
        event.stopPropagation();
        this.dropdownOpen = false;
        console.log('#log 1021 close',event, this.dropdownOpen );
    };

    render() {
        const {
            activeType,
            children,
            dragHandle,
            icons,
            onCollapse,
            onExpand,
            onRemove,
            onSettingsClick,
            types,
        } = this.props;

        const expanded = this.props.expanded || (!onCollapse && !onExpand);

        const blockClass = classNames(
            blockStyles.block,
            {
                [blockStyles.expanded]: expanded,
            }
        );

        return (
            <section className={blockClass} onClick={this.handleExpand} role="switch">
                {dragHandle &&
                    <div className={blockStyles.handle}>
                        {dragHandle}
                    </div>
                }
                <div className={blockStyles.content}>
                    <header className={blockStyles.header}>
                        {expanded
                            ? <Fragment>
                                {types && Object.keys(types).length > 1 &&
                                    <div className={blockStyles.types}>
                                        <SingleSelect onChange={this.handleTypeChange} value={activeType}>
                                            {Object.keys(types).map((key) => (
                                                // $FlowFixMe
                                                <SingleSelect.Option key={key} value={key}>
                                                    {types[key]}
                                                </SingleSelect.Option>
                                            ))}
                                        </SingleSelect>
                                    </div>
                                }
                                {icons &&
                                    <div className={blockStyles.icons}>
                                        {icons.map((icon) => <Icon key={icon} name={icon} />)}
                                    </div>
                                }
                                <div className={blockStyles.iconButtons}>
                                    {onSettingsClick && <Icon name="su-cog" onClick={onSettingsClick} />}
                                    {onRemove && <Icon name="su-trash-alt" onClick={onRemove} />}
                                    {onCollapse && onExpand &&
                                        <Icon name="su-angle-up" onClick={this.handleCollapse} />
                                    }
                                </div>
                            </Fragment>
                            : <Fragment>
                                {icons &&
                                    <div className={blockStyles.icons}>
                                        {icons.map((icon) => <Icon key={icon} name={icon} />)}
                                    </div>
                                }
                                {types && activeType && <div className={blockStyles.type}>{types[activeType]}</div>}
                                {onCollapse && onExpand && <Icon name="su-angle-down" />}
                            </Fragment>
                        }
                    </header>
                    <article className={blockStyles.children}>{children}</article>
                    <button
                            className={blockStyles.moreButton}
                            onClick={this.handleDropdownToggle}
                            ref={this.setDropdownButtonRef}
                        >
                            <Icon name="su-more-horizontal" />
                        </button>
                    <Popover
                            anchorElement={this.dropdownButtonRef || undefined}
                            onClose={this.handleDropdownClose}
                            open={this.dropdownOpen}
                        >
                            {
                                (setPopoverRef, styles) => (
                                    <div ref={setPopoverRef} style={styles}>
                                            <section>AAA</section>
                                    </div>
                                )
                            }
                    </Popover>
                </div>
            </section>
        );
    }
}
