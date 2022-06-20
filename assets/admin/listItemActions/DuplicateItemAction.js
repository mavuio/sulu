import {translate} from 'sulu-admin-bundle/utils';
import {AbstractListItemAction} from 'sulu-admin-bundle/views';
import {ResourceRequester} from 'sulu-admin-bundle/services';


export default class DuplicateItemAction extends AbstractListItemAction {
    
    resourceKey='dekors';

    getItemActionConfig(item) {

        return {
            icon: 'su-copy',
            disabled: false,
            onClick: item ? () => this.handleClick(item) : undefined,
        };
    }

    handleClick = (item) => {
        ResourceRequester
        .put(this.resourceKey, {}, {action: 'duplicate',id: item.id})
        .then(() => {
            this.list.reload();
        });
    };
}
