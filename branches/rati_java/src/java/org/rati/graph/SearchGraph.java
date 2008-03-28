/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.rati.graph;

import java.util.Iterator;
import java.util.List;
import org.apache.cayenne.auto.rati.SearchIndex;
import org.apache.cayenne.exp.Expression;
import org.apache.cayenne.exp.ExpressionFactory;
import org.apache.cayenne.query.SelectQuery;
import org.rati.global.Context;

/**
 *
 * @author terry
 */
public class SearchGraph extends Thread implements Iterable {

    private String query = null;
    private List results = null;

    public SearchGraph(String query) {
        this.query = this.prepQuery(query);
    }

    @Override
    public void run() {
        Expression qual = ExpressionFactory.likeIgnoreCaseExp(SearchIndex.OBJECT_NAME_PROPERTY, this.query);
        qual = qual.orExp(ExpressionFactory.likeIgnoreCaseExp(SearchIndex.COMBINED_ATTRIBUTES_PROPERTY, this.query));
        SelectQuery select = new SelectQuery(SearchIndex.class, qual);
        select.addOrdering(SearchIndex.RANK_PROPERTY, true);
        
        results = Context.getContext().performQuery(select);

    }

    public Iterator iterator() {
        return this.results.iterator();
    }
    
    public List getResults(){
        return this.results;
    }

    public String prepQuery(String query) {
        String temp = query.trim();
        temp = temp.replaceAll("\\s+", "%");
        temp = "%" + temp + "%";
        return temp;

    }
}
