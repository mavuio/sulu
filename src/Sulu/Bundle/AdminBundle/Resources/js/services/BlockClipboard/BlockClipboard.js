// @flow
// import {isArrayLike} from 'mobx';
// import RequestPromise from './RequestPromise';
// import type {HandleResponseHook} from './types';

import { toJS } from 'mobx';
import BlockCollection from '../../components/BlockCollection';
import userStore from '../../stores/userStore';
const USER_SETTING_CLIPBOARD = 'sulu_page.block_clipboard';


class BlockClipboard {


    setClipboardItems(items) {
        userStore.setPersistentSetting(USER_SETTING_CLIPBOARD, [...items]);
    }

    getClipboardItems() {
        return userStore.getPersistentSetting(USER_SETTING_CLIPBOARD);
    }

    insertItemsInPosition(existingBlocks,blocksToPaste,position,generatedBlockIds,expandedBlocks) {

        let expandFlags=[];
        let blockIds=[];

        for (let index = 0; index < blocksToPaste.length; index++) {
            expandFlags.push(false);
            blockIds.push(++BlockCollection.idCounter);
        }
        expandedBlocks.splice(position,0,...expandFlags);
        generatedBlockIds.splice(position,0,...blockIds);
        existingBlocks.splice(position,0,...blocksToPaste);

        return [...existingBlocks];
    }


    deleteItems(existingBlocks,positions) {
        return [...existingBlocks.filter(  (block,position) => !positions.includes(position) )];
    }

    hideItem(existingBlocks,position) {
        existingBlocks[position]['settings']['hidden']=true;
        return [...existingBlocks];
    }

    unhideItem(existingBlocks,position) {
        existingBlocks[position]['settings']['hidden']=false;
        return [...existingBlocks];
    }

    selectionCommand(command, multiselect, existingBlocks,args) {

        switch (command) {
            case "start_multiselect":
                multiselect.enabled=true;
                multiselect.selectedIndexes=[];
                break;
            case "toggle":
                if(multiselect.selectedIndexes.includes(args.position)) {
                    const indexOf=multiselect.selectedIndexes.indexOf(args.position);
                    multiselect.selectedIndexes.splice(indexOf,1);
                } else {
                    multiselect.selectedIndexes.push(args.position);
                }
                break;
            }
            console.log('#log 7131 selectionCommand',multiselect,args);
    }


}

export default new BlockClipboard();
