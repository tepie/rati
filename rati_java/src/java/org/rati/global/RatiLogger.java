/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package org.rati.global;

import org.apache.log4j.BasicConfigurator;
import org.apache.log4j.Logger;

/**
 *
 * @author terry
 */
public class RatiLogger {
    
    public static Logger getLogger(Class classObj){
        Logger log = Logger.getLogger(classObj);
        return log;
        /*
        if(log.getAllAppenders().hasMoreElements()){
            return log;
        } else {
            BasicConfigurator.configure();
            return log;
        }
        */
    }

}
