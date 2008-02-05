/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.global;

import org.apache.cayenne.access.DataContext;

/**
 *
 * @author terry
 */
public final class Context {

    public static DataContext getContext() {
        DataContext context = null;
        try {
            context = DataContext.getThreadDataContext();
        } catch (IllegalStateException ex) {
            context = DataContext.createDataContext();
            DataContext.bindThreadDataContext(context);

        }
        return context;
    }

}
